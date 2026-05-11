<?php

namespace Models\Group;

use Models\Model;

class UserGroupModel extends Model {

    protected string $table = 'user_group';

    public function getMembersForGroup(int $groupId): array {
        $this->db->query(
            'SELECT u.id, u.username, u.first_name, u.last_name, u.email, u.is_active,
                    r.name AS role_name, r.color AS role_color
             FROM user_group ug
             JOIN `user` u ON u.id = ug.id_user
             LEFT JOIN user_role ur ON ur.id_user = u.id
             LEFT JOIN role r ON r.id = ur.id_role
             WHERE ug.id_group = ?
             ORDER BY u.username ASC',
            [$groupId]
        );
        return $this->db->fetchAll();
    }

    public function getGroupsForUser(int $userId): array {
        $this->db->query(
            'SELECT g.id, g.name, g.description
             FROM user_group ug
             JOIN `group` g ON g.id = ug.id_group
             WHERE ug.id_user = ?
             ORDER BY g.name ASC',
            [$userId]
        );
        return $this->db->fetchAll();
    }

    public function isMember(int $groupId, int $userId): bool {
        $this->db->query(
            'SELECT 1 FROM user_group WHERE id_group = ? AND id_user = ? LIMIT 1',
            [$groupId, $userId]
        );
        return !empty($this->db->fetchAll());
    }

    public function addMember(int $groupId, int $userId): void {
        if (!$this->isMember($groupId, $userId)) {
            $this->insert(['id_group' => $groupId, 'id_user' => $userId, 'member_since' => time()]);
        }
    }

    public function removeMember(int $groupId, int $userId): void {
        $this->db->query(
            'DELETE FROM user_group WHERE id_group = ? AND id_user = ?',
            [$groupId, $userId]
        );
    }

    public function removeAllForGroup(int $groupId): void {
        $this->db->query('DELETE FROM user_group WHERE id_group = ?', [$groupId]);
    }

    public function countForGroup(int $groupId): int {
        $this->db->query(
            'SELECT COUNT(*) AS total FROM user_group WHERE id_group = ?',
            [$groupId]
        );
        $result = $this->db->fetchAll();
        return (int) ($result[0]['total'] ?? 0);
    }

}