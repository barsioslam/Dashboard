<?php

namespace Models\File;

use Models\Model;

class FolderModel extends Model {

    protected string $table = 'folder';

    public function isPersonal(array $folder): bool {
        return (bool) ($folder['is_personal'] ?? false);
    }

    public function getPersonalForUser(int $userId): array {
        $this->db->query(
            'SELECT f.*, (SELECT COUNT(*) FROM file fi WHERE fi.folder = f.id) AS file_count
             FROM folder f
             JOIN user_folder uf ON uf.id_folder = f.id AND uf.id_user = ?
             WHERE f.from_folder IS NULL AND f.project_id IS NULL AND f.is_personal = 1
             ORDER BY f.name ASC',
            [$userId]
        );
        return $this->db->fetchAll();
    }

    public function getSharedForUser(int $userId): array {
        $this->db->query(
            'SELECT f.*, (SELECT COUNT(*) FROM file fi WHERE fi.folder = f.id) AS file_count
             FROM folder f
             JOIN user_folder uf ON uf.id_folder = f.id AND uf.id_user = ?
             WHERE f.from_folder IS NULL AND f.project_id IS NULL AND f.is_personal = 0
             ORDER BY f.name ASC',
            [$userId]
        );
        return $this->db->fetchAll();
    }

    public function getForProject(int $projectId): array {
        $this->db->query(
            'SELECT f.*, (SELECT COUNT(*) FROM file fi WHERE fi.folder = f.id) AS file_count
             FROM folder f
             WHERE f.from_folder IS NULL AND f.project_id = ?
             ORDER BY f.name ASC',
            [$projectId]
        );
        return $this->db->fetchAll();
    }

    public function getSubfolders(int $parentId): array {
        $this->db->query(
            'SELECT f.*, (SELECT COUNT(*) FROM file fi WHERE fi.folder = f.id) AS file_count
             FROM folder f
             WHERE f.from_folder = ?
             ORDER BY f.name ASC',
            [$parentId]
        );
        return $this->db->fetchAll();
    }

    public function getPath(int $folderId): array {
        $path  = [];
        $limit = 20;
        $current = $this->getById($folderId);
        while ($current && $limit-- > 0) {
            array_unshift($path, $current);
            if (!$current['from_folder']) break;
            $current = $this->getById((int) $current['from_folder']);
        }
        return $path;
    }

    public function getRoot(int $folderId): ?array {
        $path = $this->getPath($folderId);
        return $path[0] ?? null;
    }

    public function canAccess(int $folderId, int $userId): bool {
        $root = $this->getRoot($folderId);
        if (!$root) return false;

        if ($root['project_id']) {
            $this->db->query(
                'SELECT 1 FROM user_project WHERE project_id = ? AND user_id = ? LIMIT 1',
                [$root['project_id'], $userId]
            );
            return !empty($this->db->fetchAll());
        }

        $this->db->query(
            'SELECT 1 FROM user_folder WHERE id_folder = ? AND id_user = ? LIMIT 1',
            [$root['id'], $userId]
        );
        return !empty($this->db->fetchAll());
    }

    public function create(string $name, ?int $parentId, int $userId, ?int $projectId = null, bool $isPersonal = false): int {
        $id = $this->insert([
            'name'        => $name,
            'from_folder' => $parentId,
            'project_id'  => $projectId,
            'is_personal' => ($isPersonal && !$parentId && !$projectId) ? 1 : 0,
            'created_at'  => time(),
        ]);

        if (!$projectId && !$parentId) {
            (new UserFolderModel())->addAccess($id, $userId);
        }

        return $id;
    }

    public function deleteRecursive(int $id): void {
        $subfolders = $this->getSubfolders($id);
        foreach ($subfolders as $sub) {
            $this->deleteRecursive((int) $sub['id']);
        }

        $fileModel = new FileModel();
        $files = $fileModel->getForFolder($id);
        foreach ($files as $file) {
            $fileModel->deleteWithFile((int) $file['id']);
        }

        (new UserFolderModel())->removeAllForFolder($id);
        $this->delete($id);
    }

}
