<?php

namespace App\Controllers;

use App\Views\Genfile;
use App\Utils\Checker\AccountChecker;
use Models\File\FileModel;
use Models\File\FolderModel;
use Models\File\UserFolderModel;
use Models\File\UserFileModel;
use Models\Log\ActivityLogModel;
use Models\Project\ProjectModel;
use Models\Project\UserProjectModel;

class FilesController {

    private FileModel         $fileModel;
    private FolderModel       $folderModel;
    private UserFolderModel   $userFolderModel;
    private int               $currentUserId;

    public function __construct() {
        if (!AccountChecker::logged()) {
            header('Location: ' . AccountChecker::loginUrl());
            exit;
        }
        $this->fileModel       = new FileModel();
        $this->folderModel     = new FolderModel();
        $this->userFolderModel = new UserFolderModel();
        $this->currentUserId   = (int) ($_SESSION['user_id'] ?? 0);
    }

    public function list(): void {
        $personal = $this->folderModel->getPersonalForUser($this->currentUserId);
        $shared   = $this->folderModel->getSharedForUser($this->currentUserId);

        $pageData = [
            'title'          => 'Fichiers — TaderLafe',
            'topbar_title'   => 'Mes fichiers',
            'csslist'        => ['card', 'list', 'tables', 'form'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];

        new Genfile('files/list', $pageData, compact('personal', 'shared'));
    }

    public function folder(int $id): void {
        if (!$this->folderModel->canAccess($id, $this->currentUserId)) {
            header('Location: /files/list');
            exit;
        }

        $folder     = $this->folderModel->getById($id);
        $breadcrumb = $this->folderModel->getPath($id);
        $subfolders = $this->folderModel->getSubfolders($id);
        $files      = $this->fileModel->getForFolder($id);
        $isPersonal = $this->folderModel->isPersonal($folder);
        $isOwner    = $this->userFolderModel->hasAccess($id, $this->currentUserId);

        $pageData = [
            'title'          => htmlspecialchars($folder['name']) . ' — TaderLafe',
            'topbar_title'   => 'Dossier',
            'csslist'        => ['card', 'list', 'tables', 'form'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];

        new Genfile('files/folder', $pageData, compact(
            'folder', 'breadcrumb', 'subfolders', 'files', 'isPersonal', 'isOwner'
        ));
    }

    public function project(int $projectId): void {
        $projectModel = new ProjectModel();
        $project      = $projectModel->getById($projectId);
        if (!$project) {
            header('Location: /files/list');
            exit;
        }

        $userProjectModel = new UserProjectModel();
        if (!$userProjectModel->isMember($projectId, $this->currentUserId)) {
            header('Location: /files/list');
            exit;
        }

        $folders = $this->folderModel->getForProject($projectId);

        $pageData = [
            'title'          => 'Fichiers — ' . htmlspecialchars($project['name']) . ' — TaderLafe',
            'topbar_title'   => 'Fichiers du projet',
            'csslist'        => ['card', 'list', 'tables', 'form'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];

        new Genfile('files/project', $pageData, compact('project', 'folders', 'projectId'));
    }

    public function createFolder(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /files/list');
            exit;
        }

        $name       = trim($_POST['name']       ?? '');
        $parentId   = ($_POST['parent_id']  ?? '') !== '' ? (int) $_POST['parent_id']  : null;
        $projectId  = ($_POST['project_id'] ?? '') !== '' ? (int) $_POST['project_id'] : null;
        $isPersonal = isset($_POST['is_personal']) && $_POST['is_personal'] === '1';

        if (strlen($name) < 1 || strlen($name) > 50) {
            $this->redirectBack($parentId, $projectId);
        }

        if ($parentId && !$this->folderModel->canAccess($parentId, $this->currentUserId)) {
            header('Location: /files/list');
            exit;
        }

        $id = $this->folderModel->create($name, $parentId, $this->currentUserId, $projectId, $isPersonal);
        (new ActivityLogModel())->log('folder_created', $this->currentUserId, $name);

        header('Location: /files/folder/' . $id);
        exit;
    }

    public function renameFolder(int $id): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /files/folder/' . $id);
            exit;
        }

        if (!$this->folderModel->canAccess($id, $this->currentUserId)) {
            header('Location: /files/list');
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        if (strlen($name) >= 1 && strlen($name) <= 50) {
            $folder = $this->folderModel->getById($id);
            $this->folderModel->update($id, ['name' => $name]);
            (new ActivityLogModel())->log('folder_renamed', $this->currentUserId, $name, $folder['name'] ?? null);
        }

        header('Location: /files/folder/' . $id);
        exit;
    }

    public function deleteFolder(int $id): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /files/list');
            exit;
        }

        if (!$this->folderModel->canAccess($id, $this->currentUserId)) {
            header('Location: /files/list');
            exit;
        }

        $folder   = $this->folderModel->getById($id);
        $parentId = $folder['from_folder'] ?? null;
        $projId   = $folder['project_id']  ?? null;

        $this->folderModel->deleteRecursive($id);
        (new ActivityLogModel())->log('folder_deleted', $this->currentUserId, $folder['name'] ?? null);

        if ($parentId) {
            header('Location: /files/folder/' . $parentId);
        } elseif ($projId) {
            header('Location: /files/project/' . $projId);
        } else {
            header('Location: /files/list');
        }
        exit;
    }

    public function upload(int $folderId): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /files/folder/' . $folderId);
            exit;
        }

        if (!$this->folderModel->canAccess($folderId, $this->currentUserId)) {
            header('Location: /files/list');
            exit;
        }

        if (empty($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            header('Location: /files/folder/' . $folderId);
            exit;
        }

        $displayName = trim($_POST['display_name'] ?? '') ?: $_FILES['file']['name'];
        $fileId      = $this->fileModel->store($_FILES['file'], $displayName, $folderId, $this->currentUserId);

        if ($fileId) {
            (new ActivityLogModel())->log('file_uploaded', $this->currentUserId, $displayName);
        }

        header('Location: /files/folder/' . $folderId);
        exit;
    }

    public function download(int $id): void {
        $file = $this->fileModel->getById($id);
        if (!$file) {
            header('Location: /files/list');
            exit;
        }

        $folder = $this->folderModel->getById((int) $file['folder']);
        if (!$folder || !$this->folderModel->canAccess((int) $folder['id'], $this->currentUserId)) {
            header('Location: /files/list');
            exit;
        }

        $path = $this->fileModel->getFilePath($file);
        if (!file_exists($path)) {
            header('Location: /files/folder/' . $file['folder']);
            exit;
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . addslashes($file['name']) . '"');
        header('Content-Length: ' . filesize($path));
        header('Cache-Control: must-revalidate');
        ob_clean();
        flush();
        $fp = fopen($path, 'rb');
        while (!feof($fp)) {
            echo fread($fp, 8192);
            flush();
        }
        fclose($fp);
        exit;
    }

    public function deleteFile(int $id): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /files/list');
            exit;
        }

        $file = $this->fileModel->getById($id);
        if (!$file) {
            header('Location: /files/list');
            exit;
        }

        if (!$this->folderModel->canAccess((int) $file['folder'], $this->currentUserId)) {
            header('Location: /files/list');
            exit;
        }

        $folderId = $file['folder'];
        $this->fileModel->deleteWithFile($id);
        (new ActivityLogModel())->log('file_deleted', $this->currentUserId, $file['name']);

        header('Location: /files/folder/' . $folderId);
        exit;
    }

    public function toggleShare(int $id): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /files/list');
            exit;
        }

        $file = $this->fileModel->getById($id);
        if (!$file) {
            header('Location: /files/list');
            exit;
        }

        if (!$this->folderModel->canAccess((int) $file['folder'], $this->currentUserId)) {
            header('Location: /files/list');
            exit;
        }

        if ($file['access_code']) {
            $this->fileModel->revokeAccessCode($id);
        } else {
            $this->fileModel->generateAccessCode($id);
        }

        header('Location: /files/folder/' . $file['folder']);
        exit;
    }

    public function shared(string $code): void {
        $file = $this->fileModel->getByAccessCode($code);
        if (!$file) {
            header('Location: /files/list');
            exit;
        }

        $path = $this->fileModel->getFilePath($file);
        if (!file_exists($path)) {
            header('Location: /files/list');
            exit;
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . addslashes($file['name']) . '"');
        header('Content-Length: ' . filesize($path));
        header('Cache-Control: must-revalidate');
        ob_clean();
        flush();
        $fp = fopen($path, 'rb');
        while (!feof($fp)) {
            echo fread($fp, 8192);
            flush();
        }
        fclose($fp);
        exit;
    }

    private function redirectBack(?int $parentId, ?int $projectId): never {
        if ($parentId) {
            header('Location: /files/folder/' . $parentId);
        } elseif ($projectId) {
            header('Location: /files/project/' . $projectId);
        } else {
            header('Location: /files/list');
        }
        exit;
    }

}
