<?php

namespace Models\File;

use Models\Model;

class UserFileModel extends Model {

    protected string $table = 'user_file';

    public function hasAccess(int $fileId, int $userId): bool {
        $this->db->query(
            'SELECT 1 FROM user_file WHERE id_file = ? AND id_user = ? LIMIT 1',
            [$fileId, $userId]
        );
        return !empty($this->db->fetchAll());
    }

    public function addAccess(int $fileId, int $userId): void {
        if (!$this->hasAccess($fileId, $userId)) {
            $this->insert(['id_user' => $userId, 'id_file' => $fileId]);
        }
    }

    public function removeAllForFile(int $fileId): void {
        $this->db->query('DELETE FROM user_file WHERE id_file = ?', [$fileId]);
    }

}
