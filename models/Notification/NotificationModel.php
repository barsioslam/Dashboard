<?php

namespace Models\Notification;

use Models\Model;

class NotificationModel extends Model {

    protected string $table = 'notification';

    public function countActive(): int {
        $now = time();
        $this->db->query(
            'SELECT COUNT(*) AS total FROM `notification`
             WHERE (end_date IS NULL OR end_date > ?) AND (start_date IS NULL OR start_date <= ?)',
            [$now, $now]
        );
        $result = $this->db->fetchAll();
        return (int) ($result[0]['total'] ?? 0);
    }

}
