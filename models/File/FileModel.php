<?php

namespace Models\File;

use Models\Model;

class FileModel extends Model {

    protected string $table = 'file';

    public function getForFolder(int $folderId): array {
        $this->db->query(
            'SELECT f.id, f.real_name, f.name, f.access_code,
                    uf.id_user AS uploader_id, u.username AS uploader
             FROM file f
             LEFT JOIN user_file uf ON uf.id_file = f.id
             LEFT JOIN `user` u ON u.id = uf.id_user
             WHERE f.folder = ?
             ORDER BY f.name ASC',
            [$folderId]
        );
        return $this->db->fetchAll();
    }

    public function getByAccessCode(string $code): ?array {
        $this->db->query(
            'SELECT * FROM file WHERE access_code = ? LIMIT 1',
            [substr($code, 0, 16)]
        );
        return $this->db->fetchAll()[0] ?? null;
    }

    public function store(array $uploadedFile, string $displayName, int $folderId, int $uploaderId): int {
        $ext      = strtolower(pathinfo($uploadedFile['name'], PATHINFO_EXTENSION));
        $realName = uniqid('f_', true) . ($ext ? '.' . $ext : '');
        $dest     = UPLOAD_PATH . 'files/' . $realName;

        if (!is_dir(UPLOAD_PATH . 'files/')) {
            mkdir(UPLOAD_PATH . 'files/', 0755, true);
        }

        if (!move_uploaded_file($uploadedFile['tmp_name'], $dest)) {
            return 0;
        }

        $id = $this->insert([
            'real_name' => $realName,
            'name'      => $displayName ?: $uploadedFile['name'],
            'folder'    => $folderId,
        ]);

        (new UserFileModel())->addAccess($id, $uploaderId);
        return $id;
    }

    public function deleteWithFile(int $id): void {
        $file = $this->getById($id);
        if (!$file) return;

        $path = UPLOAD_PATH . 'files/' . $file['real_name'];
        if (file_exists($path)) {
            unlink($path);
        }

        (new UserFileModel())->removeAllForFile($id);
        $this->delete($id);
    }

    public function getFilePath(array $file): string {
        return UPLOAD_PATH . 'files/' . $file['real_name'];
    }

    public function generateAccessCode(int $id): string {
        $code = bin2hex(random_bytes(8));
        $this->update($id, ['access_code' => $code]);
        return $code;
    }

    public function revokeAccessCode(int $id): void {
        $this->update($id, ['access_code' => null]);
    }

}
