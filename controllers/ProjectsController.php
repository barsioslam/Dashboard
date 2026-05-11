<?php

namespace App\Controllers;

use App\Views\Genfile;
use App\Utils\Checker\AccountChecker;
use Models\Project\ProjectModel;
use Models\Project\UserProjectModel;
use Models\Project\BugModel;
use Models\Project\BugStatusModel;
use Models\User\UserModel;
use Models\Log\ActivityLogModel;

class ProjectsController {

    private ProjectModel     $projectModel;
    private UserProjectModel $userProjectModel;
    private BugModel         $bugModel;
    private UserModel        $userModel;
    private int              $currentUserId;

    public function __construct() {
        if (!AccountChecker::logged()) {
            header('Location: /auth/login');
            exit;
        }
        $this->projectModel     = new ProjectModel();
        $this->userProjectModel = new UserProjectModel();
        $this->bugModel         = new BugModel();
        $this->userModel        = new UserModel();
        $this->currentUserId    = (int) ($_SESSION['user_id'] ?? 0);
    }

    public function list(): void {
        $search      = trim($_GET['q']      ?? '');
        $status      = trim($_GET['status'] ?? '');
        $projectpage = max(1, (int) ($_GET['page'] ?? 1));
        $perPage     = 20;
        $offset      = ($projectpage - 1) * $perPage;

        $total    = $this->projectModel->countFiltered($search, $status);
        $projects = $this->projectModel->getAllWithStats($perPage, $offset, $search, $status);
        $pages    = max(1, (int) ceil($total / $perPage));

        $pageData = [
            'title'          => 'Projets — TaderLafe',
            'topbar_title'   => 'Projets',
            'csslist'        => ['card', 'list', 'tables', 'form'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];

        new Genfile('projects/list', $pageData, compact(
            'projects', 'total', 'search', 'status', 'projectpage', 'pages'
        ));
    }

    public function view(int $id): void {
        $project = $this->projectModel->getById($id);
        if (!$project) {
            header('Location: /projects/list');
            exit;
        }

        $members    = $this->userProjectModel->getMembersForProject($id);
        $memberIds  = array_column($members, 'id');
        $allUsers   = $this->userModel->getAllWithRoles(200, 0);
        $nonMembers = array_filter($allUsers, fn($u) => !in_array((int) $u['id'], $memberIds));

        $recentBugs = array_slice($this->bugModel->getForProject($id), 0, 5);
        $bugCounts  = $this->bugModel->countAllStatuses($id);

        $pageData = [
            'title'          => htmlspecialchars($project['name']) . ' — TaderLafe',
            'topbar_title'   => 'Fiche projet',
            'csslist'        => ['card', 'list', 'tables', 'form'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];

        new Genfile('projects/view', $pageData, compact(
            'project', 'members', 'nonMembers', 'recentBugs', 'bugCounts'
        ));
    }

    public function create(): void {
        $messages = [];
        $input    = ['name' => '', 'description' => '', 'is_read_only' => 0];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input['name']        = trim($_POST['name']        ?? '');
            $input['description'] = trim($_POST['description'] ?? '');
            $input['is_read_only'] = isset($_POST['is_read_only']) ? 1 : 0;

            if (strlen($input['name']) < 2 || strlen($input['name']) > 80) {
                $messages['name'][] = 'Entre 2 et 80 caractères.';
            }

            if (empty($messages)) {
                $newId = $this->projectModel->insert([
                    'name'        => $input['name'],
                    'description' => $input['description'] ?: null,
                    'is_read_only' => $input['is_read_only'],
                    'created_at'  => time(),
                ]);
                $this->userProjectModel->addMember($newId, $this->currentUserId);
                (new ActivityLogModel())->log('project_created', $this->currentUserId, $input['name']);
                header('Location: /projects/view/' . $newId);
                exit;
            }
        }

        $pageData = [
            'title'          => 'Nouveau projet — TaderLafe',
            'topbar_title'   => 'Créer un projet',
            'csslist'        => ['card', 'form'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];

        new Genfile('projects/create', $pageData, compact('messages', 'input'));
    }

    public function edit(int $id): void {
        $project = $this->projectModel->getById($id);
        if (!$project) {
            header('Location: /projects/list');
            exit;
        }

        $messages = [];
        $prevName = $project['name'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name        = trim($_POST['name']        ?? '');
            $description = trim($_POST['description'] ?? '');
            $isReadOnly  = isset($_POST['is_read_only']) ? 1 : 0;

            if (strlen($name) < 2 || strlen($name) > 80) {
                $messages['name'][] = 'Entre 2 et 80 caractères.';
            }

            if (empty($messages)) {
                $this->projectModel->update($id, [
                    'name'        => $name,
                    'description' => $description ?: null,
                    'is_read_only' => $isReadOnly,
                ]);
                (new ActivityLogModel())->log('project_updated', $this->currentUserId, $name, $prevName);
                $messages['success'] = true;
                $project = $this->projectModel->getById($id);
            } else {
                $project = array_merge($project, [
                    'name'        => $name,
                    'description' => $description,
                    'is_read_only' => $isReadOnly,
                ]);
            }
        }

        $pageData = [
            'title'          => 'Modifier ' . htmlspecialchars($project['name']) . ' — TaderLafe',
            'topbar_title'   => 'Modifier projet',
            'csslist'        => ['card', 'form'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];

        new Genfile('projects/edit', $pageData, compact('messages', 'project'));
    }

    public function delete(int $id): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /projects/list');
            exit;
        }
        $project = $this->projectModel->getById($id);
        if ($project) {
            $this->userProjectModel->removeAllForProject($id);
            $this->projectModel->delete($id);
            (new ActivityLogModel())->log('project_deleted', $this->currentUserId, $project['name']);
        }
        header('Location: /projects/list');
        exit;
    }

    public function toggleReadOnly(int $id): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /projects/list');
            exit;
        }
        $project = $this->projectModel->getById($id);
        if ($project) {
            $newState = (int) $project['is_read_only'] === 0 ? 1 : 0;
            $this->projectModel->update($id, ['is_read_only' => $newState]);
            $action = $newState ? 'project_archived' : 'project_unarchived';
            (new ActivityLogModel())->log($action, $this->currentUserId, $project['name']);
        }
        header('Location: /projects/view/' . $id);
        exit;
    }

    public function addMember(int $projectId): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /projects/view/' . $projectId);
            exit;
        }
        $userId  = (int) ($_POST['user_id'] ?? 0);
        $project = $this->projectModel->getById($projectId);
        $user    = $this->userModel->findById($userId);

        if ($project && $user) {
            $this->userProjectModel->addMember($projectId, $userId);
            (new ActivityLogModel())->log('project_member_added', $this->currentUserId, $user['username'], $project['name']);
        }
        header('Location: /projects/view/' . $projectId);
        exit;
    }

    public function removeMember(int $projectId, int $userId): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /projects/view/' . $projectId);
            exit;
        }
        $project = $this->projectModel->getById($projectId);
        $user    = $this->userModel->findById($userId);

        if ($project && $user) {
            $this->userProjectModel->removeMember($projectId, $userId);
            (new ActivityLogModel())->log('project_member_removed', $this->currentUserId, $user['username'], $project['name']);
        }
        header('Location: /projects/view/' . $projectId);
        exit;
    }

    public function bugs(int $id): void {
        $project = $this->projectModel->getById($id);
        if (!$project) {
            header('Location: /projects/list');
            exit;
        }

        $bugStatusParam = $_GET['status'] ?? '';
        $bugStatus      = $bugStatusParam !== '' ? (int) $bugStatusParam : null;
        $bugs           = $this->bugModel->getForProject($id, $bugStatus);
        $bugCounts      = $this->bugModel->countAllStatuses($id);

        $pageData = [
            'title'          => 'Bugs — ' . htmlspecialchars($project['name']) . ' — TaderLafe',
            'topbar_title'   => 'Bugs du projet',
            'csslist'        => ['card', 'list', 'tables', 'form'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];

        new Genfile('projects/bugs', $pageData, compact('project', 'bugs', 'bugStatus', 'bugStatusParam', 'bugCounts'));
    }

    public function addBug(int $projectId): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /projects/bugs/' . $projectId);
            exit;
        }
        $project = $this->projectModel->getById($projectId);
        if (!$project) {
            header('Location: /projects/list');
            exit;
        }

        $title   = trim($_POST['title']       ?? '');
        $content = trim($_POST['description'] ?? '') ?: null;

        if (strlen($title) >= 2) {
            $this->bugModel->add($projectId, $this->currentUserId, $title, $content);
            (new ActivityLogModel())->log('bug_added', $this->currentUserId, $title, $project['name']);
        }
        header('Location: /projects/bugs/' . $projectId);
        exit;
    }

    public function updateBugStatus(int $bugId): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /projects/list');
            exit;
        }
        $bug    = $this->bugModel->getById($bugId);
        $status = (int) ($_POST['status'] ?? -1);

        if ($bug && array_key_exists($status, BugStatusModel::LABELS)) {
            $this->bugModel->updateStatus($bugId, $status, $this->currentUserId);
            (new ActivityLogModel())->log('bug_status_updated', $this->currentUserId, $bug['title'], BugStatusModel::label($status));
        }
        header('Location: /projects/bugs/' . ($bug['project_id'] ?? 0));
        exit;
    }

    public function deleteBug(int $bugId): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /projects/list');
            exit;
        }
        $bug = $this->bugModel->getById($bugId);
        if ($bug) {
            $this->bugModel->delete($bugId);
            (new ActivityLogModel())->log('bug_deleted', $this->currentUserId, $bug['title']);
            header('Location: /projects/bugs/' . $bug['project_id']);
        } else {
            header('Location: /projects/list');
        }
        exit;
    }

}