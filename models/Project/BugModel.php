<?php

namespace Models\Project;

use Models\Model;

class BugModel extends Model {

    protected string $table = 'bug';

    public function getForProject(int $projectId, ?int $status = null): array {
        $params = [$projectId];
        $where  = 'b.project_id = ?';

        $statusFilter = '';
        if ($status !== null) {
            $statusFilter = 'HAVING current_status = ?';
            $params[]     = $status;
        }

        $this->db->query(
            'SELECT b.id, b.title, b.content, b.created_at,
                    u.id AS reporter_id, u.username AS reporter,
                    (SELECT bs.status FROM bug_status bs
                     WHERE bs.bug_id = b.id
                     ORDER BY bs.status_date DESC LIMIT 1) AS current_status
             FROM bug b
             LEFT JOIN `user` u ON u.id = b.user_id
             WHERE ' . $where . '
             ' . $statusFilter . '
             ORDER BY b.created_at DESC',
            $params
        );
        $rows = $this->db->fetchAll();
        foreach ($rows as &$row) {
            $row['status'] = (int) ($row['current_status'] ?? BugStatusModel::OPEN);
        }
        return $rows;
    }

    public function countForProject(int $projectId, ?int $status = null): int {
        if ($status === null) {
            $this->db->query('SELECT COUNT(*) AS total FROM bug WHERE project_id = ?', [$projectId]);
            $result = $this->db->fetchAll();
            return (int) ($result[0]['total'] ?? 0);
        }

        $this->db->query(
            'SELECT COUNT(*) AS total
             FROM bug b
             WHERE b.project_id = ?
               AND (SELECT bs.status FROM bug_status bs
                    WHERE bs.bug_id = b.id
                    ORDER BY bs.status_date DESC LIMIT 1) = ?',
            [$projectId, $status]
        );
        $result = $this->db->fetchAll();
        return (int) ($result[0]['total'] ?? 0);
    }

    public function countAllStatuses(int $projectId): array {
        $this->db->query(
            'SELECT (SELECT bs.status FROM bug_status bs
                     WHERE bs.bug_id = b.id
                     ORDER BY bs.status_date DESC LIMIT 1) AS current_status,
                    COUNT(*) AS total
             FROM bug b
             WHERE b.project_id = ?
             GROUP BY current_status',
            [$projectId]
        );
        $rows   = $this->db->fetchAll();
        $counts = array_fill_keys(array_keys(BugStatusModel::LABELS), 0);
        foreach ($rows as $row) {
            $counts[(int) $row['current_status']] = (int) $row['total'];
        }
        return $counts;
    }

    public function add(int $projectId, int $userId, string $title, ?string $content): int {
        $id = $this->insert([
            'project_id' => $projectId,
            'user_id'    => $userId,
            'title'      => $title,
            'content'    => $content,
            'created_at' => time(),
        ]);
        (new BugStatusModel())->log($id, BugStatusModel::OPEN, $userId);
        return $id;
    }

    public function updateStatus(int $id, int $status, int $updatedBy): void {
        if (!array_key_exists($status, BugStatusModel::LABELS)) {
            return;
        }
        (new BugStatusModel())->log($id, $status, $updatedBy);
    }

}
