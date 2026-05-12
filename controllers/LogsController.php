<?php

namespace App\Controllers;

use App\Views\Genfile;
use App\Utils\Checker\AccountChecker;
use Models\Log\ActivityLogModel;
use Models\User\UserModel;

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
        $search   = trim($_GET['q']      ?? '');
        $action   = trim($_GET['action'] ?? '');
        $userId   = (int) ($_GET['user'] ?? 0);
        $logspage = max(1, (int) ($_GET['page'] ?? 1));
        $perPage  = 30;
        $offset   = ($logspage - 1) * $perPage;

        $total = $this->logModel->countFiltered($search, $action, $userId);
        $logs  = $this->logModel->getAllFiltered($perPage, $offset, $search, $action, $userId);
        $pages = max(1, (int) ceil($total / $perPage));

        $users   = (new UserModel())->getAllWithRoles(500, 0);
        $actions = array_keys(ActivityLogModel::LABELS);

        $pageData = [
            'title'          => "Logs d'activité — TaderLafe",
            'topbar_title'   => "Logs d'activité",
            'csslist'        => ['card', 'list', 'tables', 'form'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];

        new Genfile('logs/list', $pageData, compact(
            'logs', 'total', 'pages', 'logspage',
            'search', 'action', 'userId', 'users', 'actions'
        ));
    }

    public function view(int $id): void {
        $log = $this->logModel->getByIdWithUser($id);
        if (!$log) {
            header('Location: /logs/list');
            exit;
        }

        $pageData = [
            'title'          => 'Log #' . $id . ' — TaderLafe',
            'topbar_title'   => 'Détail du log',
            'csslist'        => ['card', 'list', 'tables'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];

        new Genfile('logs/view', $pageData, compact('log'));
    }

}
