<?php

namespace Models\File;

use Models\Model;

class UserFolderModel extends Model {

    protected string $table = 'user_folder';

    public function hasAccess(int $folderId, int $userId): bool {
        $this->db->query(
            'SELECT 1 FROM user_folder WHERE id_folder = ? AND id_user = ? LIMIT 1',
            [$folderId, $userId]
        );
        return !empty($this->db->fetchAll());
    }

    public function addAccess(int $folderId, int $userId): void {
        if (!$this->hasAccess($folderId, $userId)) {
            $this->insert(['id_user' => $userId, 'id_folder' => $folderId]);
        }
    }

    public function removeAccess(int $folderId, int $userId): void {
        $this->db->query(
            'DELETE FROM user_folder WHERE id_folder = ? AND id_user = ?',
            [$folderId, $userId]
        );
    }

    public function removeAllForFolder(int $folderId): void {
        $this->db->query('DELETE FROM user_folder WHERE id_folder = ?', [$folderId]);
    }

}
