<?php

namespace Models\Project;

use Models\Model;

class BugModel extends Model {

    protected string $table = 'bug';

    public function getForProject(int $projectId, ?int $status = null): array {
        $params = [$projectId];
        $where  = 'b.project_id = ?';
        if ($status !== null) {
            $where   .= ' AND b.status = ?';
            $params[] = $status;
        }
        $this->db->query(
            'SELECT b.id, b.title, b.content, b.status, b.created_at,
                    u.id AS reporter_id, u.username AS reporter
             FROM bug b
             LEFT JOIN `user` u ON u.id = b.user_id
             WHERE ' . $where . '
             ORDER BY b.created_at DESC',
            $params
        );
        return $this->db->fetchAll();
    }

    public function countForProject(int $projectId, ?int $status = null): int {
        $params = [$projectId];
        $where  = 'project_id = ?';
        if ($status !== null) {
            $where   .= ' AND status = ?';
            $params[] = $status;
        }
        $this->db->query(
            'SELECT COUNT(*) AS total FROM bug WHERE ' . $where,
            $params
        );
        $result = $this->db->fetchAll();
        return (int) ($result[0]['total'] ?? 0);
    }

    public function updateStatus(int $id, int $status, int $updatedBy): void {
        $this->update($id, ['status' => $status]);
        (new BugStatusModel())->log($id, $status, $updatedBy);
    }

}