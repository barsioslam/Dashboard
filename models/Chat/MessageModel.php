<?php

namespace Models\Chat;

use Models\Model;

class MessageModel extends Model {

    protected string $table = 'message';

    public function countSince(int $since): int {
        $this->db->query('SELECT COUNT(*) AS total FROM `message` WHERE `created_at` >= ?', [$since]);
        $result = $this->db->fetchAll();
        return (int) ($result[0]['total'] ?? 0);
    }

}
