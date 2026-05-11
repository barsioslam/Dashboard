<?php

namespace App\Controllers;

use App\Views\Genfile;
use App\Utils\Checker\AccountChecker;
use Models\Notification\NotificationModel;

class NotificationsController {

    private NotificationModel $notificationModel;

    public function __construct() {
        if (!AccountChecker::logged()) {
            header('Location: /auth/login');
            exit;
        }
        $this->notificationModel = new NotificationModel();
    }

    public function list(): void {
        $page = [
            'title'          => 'Notifications — TaderLafe',
            'topbar_title'   => 'Notifications',
            'csslist'        => ['card', 'list', 'table'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];
        new Genfile('notifications/list', $page, []);
    }

    public function view(int $id): void {
        $page = [
            'title'          => 'Notification — TaderLafe',
            'topbar_title'   => 'Fiche notification',
            'csslist'        => ['card'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];
        new Genfile('notifications/view', $page, []);
    }

    public function create(): void {
        $page = [
            'title'          => 'Nouvelle notification — TaderLafe',
            'topbar_title'   => 'Créer une notification',
            'csslist'        => ['card', 'form'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];
        new Genfile('notifications/create', $page, []);
    }

    public function edit(int $id): void {
        $page = [
            'title'          => 'Modifier notification — TaderLafe',
            'topbar_title'   => 'Modifier notification',
            'csslist'        => ['card', 'form'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];
        new Genfile('notifications/edit', $page, []);
    }

    public function delete(int $id): void {
        header('Location: /notifications/list');
        exit;
    }

}
