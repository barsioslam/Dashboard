<?php

namespace App\Controllers;

use App\Views\Genfile;
use App\Utils\Checker\AccountChecker;
use Models\Group\GroupModel;
use Models\Group\UserGroupModel;
use Models\User\UserModel;
use Models\Log\ActivityLogModel;

class GroupsController {

    private GroupModel     $groupModel;
    private UserGroupModel $userGroupModel;
    private UserModel      $userModel;
    private int            $currentUserId;

    public function __construct() {
        if (!AccountChecker::logged()) {
            header('Location: /auth/login');
            exit;
        }
        $this->groupModel     = new GroupModel();
        $this->userGroupModel = new UserGroupModel();
        $this->userModel      = new UserModel();
        $this->currentUserId  = (int) ($_SESSION['user_id'] ?? 0);
    }

    public function list(): void {
        $search    = trim($_GET['q'] ?? '');
        $grouppage = max(1, (int) ($_GET['page'] ?? 1));
        $perPage   = 20;
        $offset    = ($grouppage - 1) * $perPage;

        $total  = $this->groupModel->countFiltered($search);
        $groups = $this->groupModel->getAllWithMemberCount($perPage, $offset, $search);
        $pages  = max(1, (int) ceil($total / $perPage));

        $pageData = [
            'title'          => 'Groupes — TaderLafe',
            'topbar_title'   => 'Groupes',
            'csslist'        => ['card', 'list', 'tables', 'form'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];

        new Genfile('groups/list', $pageData, compact('groups', 'total', 'search', 'grouppage', 'pages'));
    }

    public function view(int $id): void {
        $group = $this->groupModel->getById($id);
        if (!$group) {
            header('Location: /groups/list');
            exit;
        }

        $members      = $this->userGroupModel->getMembersForGroup($id);
        $memberIds    = array_column($members, 'id');
        $allUsers     = $this->userModel->getAllWithRoles(200, 0);
        $nonMembers   = array_filter($allUsers, fn($u) => !in_array((int) $u['id'], $memberIds));

        $pageData = [
            'title'          => htmlspecialchars($group['name']) . ' — TaderLafe',
            'topbar_title'   => 'Fiche groupe',
            'csslist'        => ['card', 'list', 'tables', 'form'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];

        new Genfile('groups/view', $pageData, compact('group', 'members', 'nonMembers'));
    }

    public function create(): void {
        $messages = [];
        $input    = ['name' => '', 'description' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input['name']        = trim($_POST['name']        ?? '');
            $input['description'] = trim($_POST['description'] ?? '');

            if (strlen($input['name']) < 2 || strlen($input['name']) > 60) {
                $messages['name'][] = 'Entre 2 et 60 caractères.';
            }

            if (empty($messages)) {
                $newId = $this->groupModel->insert([
                    'name'        => $input['name'],
                    'description' => $input['description'] ?: null,
                    'created_at'  => time(),
                ]);
                (new ActivityLogModel())->log('group_created', $this->currentUserId, $input['name']);
                header('Location: /groups/view/' . $newId);
                exit;
            }
        }

        $pageData = [
            'title'          => 'Nouveau groupe — TaderLafe',
            'topbar_title'   => 'Créer un groupe',
            'csslist'        => ['card', 'form'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];

        new Genfile('groups/create', $pageData, compact('messages', 'input'));
    }

    public function edit(int $id): void {
        $group = $this->groupModel->getById($id);
        if (!$group) {
            header('Location: /groups/list');
            exit;
        }

        $messages = [];
        $prevName = $group['name'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name        = trim($_POST['name']        ?? '');
            $description = trim($_POST['description'] ?? '');

            if (strlen($name) < 2 || strlen($name) > 60) {
                $messages['name'][] = 'Entre 2 et 60 caractères.';
            }

            if (empty($messages)) {
                $this->groupModel->update($id, [
                    'name'        => $name,
                    'description' => $description ?: null,
                ]);
                (new ActivityLogModel())->log('group_updated', $this->currentUserId, $name, $prevName);
                $messages['success'] = true;
                $group = $this->groupModel->getById($id);
            } else {
                $group = array_merge($group, ['name' => $name, 'description' => $description]);
            }
        }

        $pageData = [
            'title'          => 'Modifier ' . htmlspecialchars($group['name']) . ' — TaderLafe',
            'topbar_title'   => 'Modifier groupe',
            'csslist'        => ['card', 'form'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];

        new Genfile('groups/edit', $pageData, compact('messages', 'group'));
    }

    public function delete(int $id): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /groups/list');
            exit;
        }
        $group = $this->groupModel->getById($id);
        if ($group) {
            $this->userGroupModel->removeAllForGroup($id);
            $this->groupModel->delete($id);
            (new ActivityLogModel())->log('group_deleted', $this->currentUserId, $group['name']);
        }
        header('Location: /groups/list');
        exit;
    }

    public function addMember(int $groupId): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /groups/view/' . $groupId);
            exit;
        }
        $userId = (int) ($_POST['user_id'] ?? 0);
        $group  = $this->groupModel->getById($groupId);
        $user   = $this->userModel->findById($userId);

        if ($group && $user) {
            $this->userGroupModel->addMember($groupId, $userId);
            (new ActivityLogModel())->log('group_member_added', $this->currentUserId, $user['username'], $group['name']);
        }
        header('Location: /groups/view/' . $groupId);
        exit;
    }

    public function removeMember(int $groupId, int $userId): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /groups/view/' . $groupId);
            exit;
        }
        $group = $this->groupModel->getById($groupId);
        $user  = $this->userModel->findById($userId);

        if ($group && $user) {
            $this->userGroupModel->removeMember($groupId, $userId);
            (new ActivityLogModel())->log('group_member_removed', $this->currentUserId, $user['username'], $group['name']);
        }
        header('Location: /groups/view/' . $groupId);
        exit;
    }

}