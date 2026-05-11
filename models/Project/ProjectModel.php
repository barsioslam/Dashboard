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

}
