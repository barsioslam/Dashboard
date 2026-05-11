<?php

namespace App\Controllers;

use App\Views\Genfile;
use App\Utils\Checker\AccountChecker;
use Models\Project\ProjectModel;
use Models\Project\UserProjectModel;
use Models\Project\BugModel;

class ProjectsController {

    private ProjectModel $projectModel;

    public function __construct() {
        if (!AccountChecker::logged()) {
            header('Location: /auth/login');
            exit;
        }
        $this->projectModel = new ProjectModel();
    }

    public function list(): void {
        $page = [
            'title'          => 'Projets — TaderLafe',
            'topbar_title'   => 'Projets',
            'csslist'        => ['card', 'list', 'tables'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];
        new Genfile('projects/list', $page, []);
    }

    public function view(int $id): void {
        $page = [
            'title'          => 'Projet — TaderLafe',
            'topbar_title'   => 'Fiche projet',
            'csslist'        => ['card', 'list', 'tables'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];
        new Genfile('projects/view', $page, []);
    }

    public function create(): void {
        $page = [
            'title'          => 'Nouveau projet — TaderLafe',
            'topbar_title'   => 'Créer un projet',
            'csslist'        => ['card', 'form'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];
        new Genfile('projects/create', $page, []);
    }

    public function edit(int $id): void {
        $page = [
            'title'          => 'Modifier projet — TaderLafe',
            'topbar_title'   => 'Modifier projet',
            'csslist'        => ['card', 'form'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];
        new Genfile('projects/edit', $page, []);
    }

    public function delete(int $id): void {
        header('Location: /projects/list');
        exit;
    }

    public function bugs(int $id): void {
        $page = [
            'title'          => 'Bugs — TaderLafe',
            'topbar_title'   => 'Bugs du projet',
            'csslist'        => ['card', 'list', 'tables'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];
        new Genfile('projects/bugs', $page, []);
    }

}
