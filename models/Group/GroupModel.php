<?php

namespace Models\Group;

use Models\Model;

class GroupModel extends Model {

    protected string $table = 'group';

    public function getAllWithMemberCount(int $limit, int $offset, string $search = ''): array {
        $params = [];
        $where  = '';
        if ($search !== '') {
            $like  = '%' . $search . '%';
            $where = ' WHERE (g.name LIKE ? OR g.description LIKE ?)';
            $params = [$like, $like];
        }
        $this->db->query(
            'SELECT g.id, g.name, g.description, g.created_at,
                    COUNT(ug.id_user) AS member_count
             FROM `group` g
             LEFT JOIN user_group ug ON ug.id_group = g.id'
            . $where
            . ' GROUP BY g.id'
            . ' ORDER BY g.created_at DESC'
            . ' LIMIT ' . (int) $limit . ' OFFSET ' . (int) $offset,
            $params
        );
        return $this->db->fetchAll();
    }

    public function countFiltered(string $search = ''): int {
        $params = [];
        $where  = '';
        if ($search !== '') {
            $like   = '%' . $search . '%';
            $where  = ' WHERE (name LIKE ? OR description LIKE ?)';
            $params = [$like, $like];
        }
        $this->db->query(
            'SELECT COUNT(*) AS total FROM `group`' . $where,
            $params
        );
        $result = $this->db->fetchAll();
        return (int) ($result[0]['total'] ?? 0);
    }

}
