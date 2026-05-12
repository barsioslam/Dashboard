<?php

namespace App\Controllers;

use App\Views\Genfile;
use App\Utils\Checker\AccountChecker;
use Models\User\UserModel;
use Models\User\UserSessionModel;
use Models\Permission\UserRoleModel;
use Models\Permission\RoleModel;
use Models\Log\ActivityLogModel;

class UsersController {

    private UserModel     $userModel;
    private UserRoleModel $userRoleModel;
    private RoleModel     $roleModel;
    private int           $currentUserId;

    public function __construct() {
        if (!AccountChecker::logged()) {
            header('Location: ' . AccountChecker::loginUrl());
            exit;
        }
        $this->userModel     = new UserModel();
        $this->userRoleModel = new UserRoleModel();
        $this->roleModel     = new RoleModel();
        $this->currentUserId = (int) ($_SESSION['user_id'] ?? 0);
    }

    public function list(): void {
        $search  = trim($_GET['q']      ?? '');
        $status  = trim($_GET['status'] ?? '');
        $userspage    = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 20;
        $offset  = ($userspage - 1) * $perPage;

        $total = $this->userModel->countFiltered($search, $status);
        $users = $this->userModel->getAllWithRoles($perPage, $offset, $search, $status);
        $pages = max(1, (int) ceil($total / $perPage));

        $pageData = [
            'title'          => 'Utilisateurs — TaderLafe',
            'topbar_title'   => 'Utilisateurs',
            'csslist'        => ['card', 'list', 'tables', 'form'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];

        new Genfile('users/list', $pageData, compact(
            'users', 'total', 'search', 'status', 'userspage', 'pages'
        ));
    }

    public function view(int $id): void {
        $user = $this->userModel->getWithRole($id);
        if (!$user) {
            header('Location: /users/list');
            exit;
        }

        $recentLogs = (new ActivityLogModel())->getForUser($id, 8);
        $has2FA     = $this->userModel->has2FAApp($id);
        $sessions   = (new UserSessionModel())->getByUser($id);

        $pageData = [
            'title'          => htmlspecialchars($user['username']) . ' — TaderLafe',
            'topbar_title'   => 'Fiche utilisateur',
            'csslist'        => ['card', 'list', 'form'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];

        new Genfile('users/view', $pageData, compact(
            'user', 'recentLogs', 'has2FA', 'sessions'
        ));
    }

    public function create(): void {
        $messages = [];
        $input    = [
            'first_name' => '', 'last_name' => '', 'username' => '',
            'email' => '', 'role_id' => 0, 'is_active' => 1,
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = [
                'first_name' => trim($_POST['first_name']  ?? ''),
                'last_name'  => trim($_POST['last_name']   ?? ''),
                'username'   => trim($_POST['username']    ?? ''),
                'email'      => trim($_POST['email']       ?? ''),
                'password'   => $_POST['password']         ?? '',
                'confirm'    => $_POST['confirm_password'] ?? '',
                'role_id'    => (int) ($_POST['role_id']   ?? 0),
                'is_active'  => (int) ($_POST['is_active'] ?? 1),
            ];

            if (strlen($input['username']) < 3 || strlen($input['username']) > 20) {
                $messages['username'][] = 'Entre 3 et 20 caractères.';
            } elseif ($this->userModel->findByUsername($input['username'])) {
                $messages['username'][] = 'Ce nom d\'utilisateur est déjà utilisé.';
            }
            if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
                $messages['email'][] = 'Adresse e-mail invalide.';
            } elseif ($this->userModel->findByEmail($input['email'])) {
                $messages['email'][] = 'Cet e-mail est déjà utilisé.';
            }
            if (strlen($input['password']) < 12) {
                $messages['password'][] = 'Minimum 12 caractères.';
            }
            if ($input['password'] !== $input['confirm']) {
                $messages['confirm'][] = 'Les mots de passe ne correspondent pas.';
            }

            if (empty($messages)) {
                $newId = $this->userModel->register([
                    'username'   => $input['username'],
                    'email'      => $input['email'],
                    'first_name' => $input['first_name'],
                    'last_name'  => $input['last_name'],
                    'password'   => $input['password'],
                    'is_active'  => $input['is_active'],
                ]);
                if ($input['role_id'] > 0) {
                    $this->userRoleModel->setRole($newId, $input['role_id']);
                }
                (new ActivityLogModel())->log('user_created', $this->currentUserId, $input['username']);
                header('Location: /users/view/' . $newId);
                exit;
            }
        }

        $roles = $this->roleModel->getAll();

        $pageData = [
            'title'          => 'Nouvel utilisateur — TaderLafe',
            'topbar_title'   => 'Créer un utilisateur',
            'csslist'        => ['card', 'form'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];

        new Genfile('users/create', $pageData, compact('messages', 'input', 'roles'));
    }

    public function edit(int $id): void {
        $user = $this->userModel->getWithRole($id);
        if (!$user) {
            header('Location: /users/list');
            exit;
        }

        $messages    = [];
        $prevUsername = $user['username'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username  = trim($_POST['username']    ?? '');
            $email     = trim($_POST['email']       ?? '');
            $firstName = trim($_POST['first_name']  ?? '');
            $lastName  = trim($_POST['last_name']   ?? '');
            $roleId    = (int) ($_POST['role_id']   ?? 0);
            $isActive  = (int) ($_POST['is_active'] ?? 1);
            $newPass   = $_POST['new_password']     ?? '';

            if (strlen($username) < 3 || strlen($username) > 20) {
                $messages['username'][] = 'Entre 3 et 20 caractères.';
            } elseif (($ex = $this->userModel->findByUsername($username)) && (int) $ex['id'] !== $id) {
                $messages['username'][] = 'Ce nom d\'utilisateur est déjà utilisé.';
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $messages['email'][] = 'Adresse e-mail invalide.';
            } elseif (($ex = $this->userModel->findByEmail($email)) && (int) $ex['id'] !== $id) {
                $messages['email'][] = 'Cet e-mail est déjà utilisé.';
            }
            if ($newPass !== '' && strlen($newPass) < 12) {
                $messages['new_password'][] = 'Minimum 12 caractères.';
            }

            if (empty($messages)) {
                $this->userModel->update($id, [
                    'username'   => $username,
                    'email'      => $email,
                    'first_name' => $firstName,
                    'last_name'  => $lastName,
                    'is_active'  => $isActive,
                ]);
                if ($newPass !== '') {
                    $this->userModel->updatePassword($id, $newPass);
                }
                $this->userRoleModel->setRole($id, $roleId);

                if ($id === $this->currentUserId) {
                    $_SESSION['username']   = $username;
                    $_SESSION['email']      = $email;
                    $_SESSION['first_name'] = $firstName;
                    $_SESSION['last_name']  = $lastName;
                }
                (new ActivityLogModel())->log('user_updated', $this->currentUserId, $username, $prevUsername);
                $messages['success'] = true;
                $user = $this->userModel->getWithRole($id);
            } else {
                $user = array_merge($user, [
                    'username'   => $username,
                    'email'      => $email,
                    'first_name' => $firstName,
                    'last_name'  => $lastName,
                    'is_active'  => $isActive,
                    'role_id'    => $roleId,
                ]);
            }
        }

        $roles = $this->roleModel->getAll();

        $pageData = [
            'title'          => 'Modifier ' . htmlspecialchars($user['username']) . ' — TaderLafe',
            'topbar_title'   => 'Modifier utilisateur',
            'csslist'        => ['card', 'form'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];

        new Genfile('users/edit', $pageData, compact('messages', 'user', 'roles'));
    }

    public function delete(int $id): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $id === $this->currentUserId) {
            header('Location: /users/list');
            exit;
        }
        $user = $this->userModel->findById($id);
        if ($user) {
            $this->userRoleModel->removeForUser($id);
            $this->userModel->delete($id);
            (new ActivityLogModel())->log('user_deleted', $this->currentUserId, $user['username']);
        }
        header('Location: /users/list');
        exit;
    }

    public function toggleActive(int $id): void {
        if ($id !== $this->currentUserId) {
            $user = $this->userModel->findById($id);
            if ($user) {
                $newStatus = !(bool) $user['is_active'];
                $this->userModel->setActive($id, $newStatus);
                $action = $newStatus ? 'user_activated' : 'user_deactivated';
                (new ActivityLogModel())->log($action, $this->currentUserId, $user['username']);
            }
        }
        header('Location: /users/list');
        exit;
    }

}