<?php

namespace App\Controllers;

use App\Views\Genfile;
use App\Utils\Checker\AccountChecker;
use Models\Group\GroupModel;
use Models\Group\UserGroupModel;

class GroupsController {

    private GroupModel $groupModel;

    public function __construct() {
        if (!AccountChecker::logged()) {
            header('Location: /auth/login');
            exit;
        }
        $this->groupModel = new GroupModel();
    }

    public function list(): void {
        $page = [
            'title'          => 'Groupes — TaderLafe',
            'topbar_title'   => 'Groupes',
            'csslist'        => ['card', 'list', 'tables'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];
        new Genfile('groups/list', $page, []);
    }

    public function view(int $id): void {
        $page = [
            'title'          => 'Groupe — TaderLafe',
            'topbar_title'   => 'Fiche groupe',
            'csslist'        => ['card', 'list'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];
        new Genfile('groups/view', $page, []);
    }

    public function create(): void {
        $page = [
            'title'          => 'Nouveau groupe — TaderLafe',
            'topbar_title'   => 'Créer un groupe',
            'csslist'        => ['card', 'form'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];
        new Genfile('groups/create', $page, []);
    }

    public function edit(int $id): void {
        $page = [
            'title'          => 'Modifier groupe — TaderLafe',
            'topbar_title'   => 'Modifier groupe',
            'csslist'        => ['card', 'form'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];
        new Genfile('groups/edit', $page, []);
    }

    public function delete(int $id): void {
        header('Location: /groups/list');
        exit;
    }

}
