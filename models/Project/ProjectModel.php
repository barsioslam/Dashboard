<?php

namespace Models\Project;

use Models\Model;

class ProjectModel extends Model {

    protected string $table = 'project';

    public function countActive(): int {
        return $this->countBy('is_read_only', 0);
    }

    public function getRecent(int $limit = 5): array {
        $this->db->query(
            'SELECT id, name, description, created_at, is_read_only
             FROM `project`
             ORDER BY created_at DESC
             LIMIT ' . (int) $limit
        );
        return $this->db->fetchAll();
    }

    public function getAllWithStats(int $limit, int $offset, string $search = '', string $status = ''): array {
        $params = [];
        $where  = [];
        if ($search !== '') {
            $like    = '%' . $search . '%';
            $where[] = '(p.name LIKE ? OR p.description LIKE ?)';
            $params  = [$like, $like];
        }
        if ($status === 'active')   { $where[] = 'p.is_read_only = 0'; }
        if ($status === 'archived') { $where[] = 'p.is_read_only = 1'; }

        $this->db->query(
            'SELECT p.id, p.name, p.description, p.created_at, p.is_read_only,
                    COUNT(DISTINCT up.user_id) AS member_count,
                    COUNT(DISTINCT b.id)       AS bug_count
             FROM `project` p
             LEFT JOIN user_project up ON up.project_id = p.id
             LEFT JOIN bug b ON b.project_id = p.id'
            . ($where ? ' WHERE ' . implode(' AND ', $where) : '')
            . ' GROUP BY p.id'
            . ' ORDER BY p.created_at DESC'
            . ' LIMIT ' . (int) $limit . ' OFFSET ' . (int) $offset,
            $params
        );
        return $this->db->fetchAll();
    }

    public function countFiltered(string $search = '', string $status = ''): int {
        $params = [];
        $where  = [];
        if ($search !== '') {
            $like    = '%' . $search . '%';
            $where[] = '(name LIKE ? OR description LIKE ?)';
            $params  = [$like, $like];
        }
        if ($status === 'active')   { $where[] = 'is_read_only = 0'; }
        if ($status === 'archived') { $where[] = 'is_read_only = 1'; }

        $this->db->query(
            'SELECT COUNT(*) AS total FROM `project`'
            . ($where ? ' WHERE ' . implode(' AND ', $where) : ''),
            $params
        );
        $result = $this->db->fetchAll();
        return (int) ($result[0]['total'] ?? 0);
    }

}