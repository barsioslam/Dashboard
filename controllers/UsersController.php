<?php

namespace App\Controllers;

use App\Views\Genfile;
use App\Utils\Checker\AccountChecker;
use Models\User\UserModel;
use Models\Permission\UserRoleModel;
use Models\Permission\RoleModel;

class UsersController {

    private UserModel $userModel;

    public function __construct() {
        if (!AccountChecker::logged()) {
            header('Location: /auth/login');
            exit;
        }
        $this->userModel = new UserModel();
    }

    public function list(): void {
        $page = [
            'title'          => 'Utilisateurs — TaderLafe',
            'topbar_title'   => 'Utilisateurs',
            'csslist'        => ['card', 'list', 'table'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];
        new Genfile('users/list', $page, []);
    }

    public function view(int $id): void {
        $page = [
            'title'          => 'Utilisateur — TaderLafe',
            'topbar_title'   => 'Fiche utilisateur',
            'csslist'        => ['card', 'list'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];
        new Genfile('users/view', $page, []);
    }

    public function create(): void {
        $page = [
            'title'          => 'Nouvel utilisateur — TaderLafe',
            'topbar_title'   => 'Créer un utilisateur',
            'csslist'        => ['card', 'form'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];
        new Genfile('users/create', $page, []);
    }

    public function edit(int $id): void {
        $page = [
            'title'          => 'Modifier utilisateur — TaderLafe',
            'topbar_title'   => 'Modifier utilisateur',
            'csslist'        => ['card', 'form'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];
        new Genfile('users/edit', $page, []);
    }

    public function delete(int $id): void {
        header('Location: /users/list');
        exit;
    }

    public function toggleActive(int $id): void {
        header('Location: /users/list');
        exit;
    }

}
