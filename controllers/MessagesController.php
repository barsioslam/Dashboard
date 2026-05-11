<?php

namespace App\Controllers;

use App\Views\Genfile;
use App\Utils\Checker\AccountChecker;
use Models\Chat\ChatModel;
use Models\Chat\MessageModel;
use Models\Chat\UserChatModel;

class MessagesController {

    private ChatModel $chatModel;

    public function __construct() {
        if (!AccountChecker::logged()) {
            header('Location: /auth/login');
            exit;
        }
        $this->chatModel = new ChatModel();
    }

    public function list(): void {
        $page = [
            'title'          => 'Messages — TaderLafe',
            'topbar_title'   => 'Messages',
            'csslist'        => ['card', 'list', 'tables'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];
        new Genfile('messages/list', $page, []);
    }

    public function view(int $id): void {
        $page = [
            'title'          => 'Conversation — TaderLafe',
            'topbar_title'   => 'Conversation',
            'csslist'        => ['card', 'list'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];
        new Genfile('messages/view', $page, []);
    }

    public function delete(int $id): void {
        header('Location: /messages/list');
        exit;
    }

}
