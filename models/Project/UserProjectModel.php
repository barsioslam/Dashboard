<?php

namespace Models\Project;

use Models\Model;

class UserProjectModel extends Model {

    protected string $table = 'user_project';

    public function getMembersForProject(int $projectId): array {
        $this->db->query(
            'SELECT u.id, u.username, u.first_name, u.last_name, u.email, u.is_active,
                    r.name AS role_name, r.color AS role_color, up.is_owner
             FROM user_project up
             JOIN `user` u ON u.id = up.user_id
             LEFT JOIN user_role ur ON ur.id_user = u.id
             LEFT JOIN role r ON r.id = ur.id_role
             WHERE up.project_id = ?
             ORDER BY up.is_owner DESC, u.username ASC',
            [$projectId]
        );
        return $this->db->fetchAll();
    }

    public function getProjectsForUser(int $userId): array {
        $this->db->query(
            'SELECT p.id, p.name, p.is_read_only
             FROM user_project up
             JOIN `project` p ON p.id = up.project_id
             WHERE up.user_id = ?
             ORDER BY p.name ASC',
            [$userId]
        );
        return $this->db->fetchAll();
    }

    public function isMember(int $projectId, int $userId): bool {
        $this->db->query(
            'SELECT 1 FROM user_project WHERE project_id = ? AND user_id = ? LIMIT 1',
            [$projectId, $userId]
        );
        return !empty($this->db->fetchAll());
    }

    public function addMember(int $projectId, int $userId, bool $isOwner = false): void {
        if (!$this->isMember($projectId, $userId)) {
            $this->insert([
                'project_id'  => $projectId,
                'user_id'     => $userId,
                'member_since' => time(),
                'is_owner'    => $isOwner ? 1 : 0,
            ]);
        }
    }

    public function isOwner(int $projectId, int $userId): bool {
        $this->db->query(
            'SELECT 1 FROM user_project WHERE project_id = ? AND user_id = ? AND is_owner = 1 LIMIT 1',
            [$projectId, $userId]
        );
        return !empty($this->db->fetchAll());
    }

    public function removeMember(int $projectId, int $userId): void {
        if ($this->isOwner($projectId, $userId)) {
            return;
        }
        $this->db->query(
            'DELETE FROM user_project WHERE project_id = ? AND user_id = ?',
            [$projectId, $userId]
        );
    }

    public function removeAllForProject(int $projectId): void {
        $this->db->query('DELETE FROM user_project WHERE project_id = ?', [$projectId]);
    }

}