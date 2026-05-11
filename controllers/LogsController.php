<?php

namespace App\Controllers;

use App\Views\Genfile;
use App\Utils\Checker\AccountChecker;
use Models\Log\ActivityLogModel;

class LogsController {

    private ActivityLogModel $logModel;

    public function __construct() {
        if (!AccountChecker::logged()) {
            header('Location: /auth/login');
            exit;
        }
        $this->logModel = new ActivityLogModel();
    }

    public function list(): void {
        $page = [
            'title'          => "Logs d'activité — TaderLafe",
            'topbar_title'   => "Logs d'activité",
            'csslist'        => ['card', 'list', 'table'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];
        new Genfile('logs/list', $page, []);
    }

    public function view(int $id): void {
        $page = [
            'title'          => 'Log — TaderLafe',
            'topbar_title'   => 'Détail du log',
            'csslist'        => ['card'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];
        new Genfile('logs/view', $page, []);
    }

}
