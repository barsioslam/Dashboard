<?php

namespace App\Controllers;

use App\Views\Genfile;
use App\Utils\Checker\AccountChecker;
use Models\Todo\TodoListModel;
use Models\Todo\TodoElementModel;

class TodosController {

    private TodoListModel $todoListModel;

    public function __construct() {
        if (!AccountChecker::logged()) {
            header('Location: ' . AccountChecker::loginUrl());
            exit;
        }
        $this->todoListModel = new TodoListModel();
    }

    public function list(): void {
        $page = [
            'title'          => 'Todo Lists — TaderLafe',
            'topbar_title'   => 'Todo Lists',
            'csslist'        => ['card', 'list', 'tables'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];
        new Genfile('todos/list', $page, []);
    }

    public function view(int $id): void {
        $page = [
            'title'          => 'Todo List — TaderLafe',
            'topbar_title'   => 'Todo List',
            'csslist'        => ['card', 'list'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];
        new Genfile('todos/view', $page, []);
    }

    public function create(): void {
        $page = [
            'title'          => 'Nouvelle todo list — TaderLafe',
            'topbar_title'   => 'Créer une todo list',
            'csslist'        => ['card', 'form'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];
        new Genfile('todos/create', $page, []);
    }

    public function edit(int $id): void {
        $page = [
            'title'          => 'Modifier todo list — TaderLafe',
            'topbar_title'   => 'Modifier todo list',
            'csslist'        => ['card', 'form'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];
        new Genfile('todos/edit', $page, []);
    }

    public function delete(int $id): void {
        header('Location: /todos/list');
        exit;
    }

}
