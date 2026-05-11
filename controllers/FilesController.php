<?php

namespace App\Controllers;

use App\Views\Genfile;
use App\Utils\Checker\AccountChecker;
use Models\File\FileModel;
use Models\File\FolderModel;

class FilesController {

    private FileModel   $fileModel;
    private FolderModel $folderModel;

    public function __construct() {
        if (!AccountChecker::logged()) {
            header('Location: /auth/login');
            exit;
        }
        $this->fileModel   = new FileModel();
        $this->folderModel = new FolderModel();
    }

    public function list(): void {
        $page = [
            'title'          => 'Fichiers — TaderLafe',
            'topbar_title'   => 'Fichiers',
            'csslist'        => ['card', 'list', 'table'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];
        new Genfile('files/list', $page, []);
    }

    public function folder(int $id): void {
        $page = [
            'title'          => 'Dossier — TaderLafe',
            'topbar_title'   => 'Contenu du dossier',
            'csslist'        => ['card', 'list'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];
        new Genfile('files/folder', $page, []);
    }

    public function deleteFile(int $id): void {
        header('Location: /files/list');
        exit;
    }

    public function deleteFolder(int $id): void {
        header('Location: /files/list');
        exit;
    }

}
