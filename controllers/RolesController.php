<?php

namespace App\Controllers;

use App\Views\Genfile;
use App\Utils\Checker\AccountChecker;
use Models\Permission\RoleModel;
use Models\Permission\PermissionModel;
use Models\Permission\RolePermissionModel;

class RolesController {

    private RoleModel $roleModel;

    public function __construct() {
        if (!AccountChecker::logged()) {
            header('Location: /auth/login');
            exit;
        }
        $this->roleModel = new RoleModel();
    }

    public function list(): void {
        $page = [
            'title'          => 'Rôles & Permissions — TaderLafe',
            'topbar_title'   => 'Rôles & Permissions',
            'csslist'        => ['card', 'list', 'table'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];
        new Genfile('roles/list', $page, []);
    }

    public function view(int $id): void {
        $page = [
            'title'          => 'Rôle — TaderLafe',
            'topbar_title'   => 'Fiche rôle',
            'csslist'        => ['card', 'list'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];
        new Genfile('roles/view', $page, []);
    }

    public function create(): void {
        $page = [
            'title'          => 'Nouveau rôle — TaderLafe',
            'topbar_title'   => 'Créer un rôle',
            'csslist'        => ['card', 'form'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];
        new Genfile('roles/create', $page, []);
    }

    public function edit(int $id): void {
        $page = [
            'title'          => 'Modifier rôle — TaderLafe',
            'topbar_title'   => 'Modifier rôle',
            'csslist'        => ['card', 'form'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];
        new Genfile('roles/edit', $page, []);
    }

    public function delete(int $id): void {
        header('Location: /roles/list');
        exit;
    }

}
