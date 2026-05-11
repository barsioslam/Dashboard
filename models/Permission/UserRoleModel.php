<?php

namespace Models\Permission;

use Models\Model;

class UserRoleModel extends Model {

    protected string $table = 'user_role';

    public function getForUser(int $userId): ?array {
        $result = $this->getBy('id_user', $userId);
        return $result[0] ?? null;
    }

    public function setRole(int $userId, int $roleId): void {
        $this->db->query('DELETE FROM `user_role` WHERE `id_user` = ?', [$userId]);
        if ($roleId > 0) {
            $this->insert(['id_user' => $userId, 'id_role' => $roleId]);
        }
    }

    public function removeForUser(int $userId): void {
        $this->db->query('DELETE FROM `user_role` WHERE `id_user` = ?', [$userId]);
    }

}
