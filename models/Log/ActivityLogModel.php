<?php

namespace Models\Log;

use Models\Model;

class ActivityLogModel extends Model {

    protected string $table = 'activity_log';

    public function getRecentWithUser(int $limit = 6): array {
        $this->db->query(
            'SELECT al.id, al.action, al.current, al.previous, al.activity_date, al.user_id,
                    u.username
             FROM activity_log al
             LEFT JOIN `user` u ON u.id = al.user_id
             ORDER BY al.activity_date DESC
             LIMIT ' . (int) $limit
        );
        return $this->db->fetchAll();
    }

    public function getLoginsSince(int $since): array {
        $this->db->query(
            "SELECT activity_date
             FROM activity_log
             WHERE action = 'login' AND activity_date >= ?
             ORDER BY activity_date ASC",
            [$since]
        );
        return $this->db->fetchAll();
    }

    public function getForUser(int $userId, int $limit = 8): array {
        $this->db->query(
            'SELECT * FROM `activity_log` WHERE `user_id` = ? ORDER BY `activity_date` DESC LIMIT ' . (int) $limit,
            [$userId]
        );
        return $this->db->fetchAll();
    }

    public function log(string $action, int $actorId, ?string $current = null, ?string $previous = null): void {
        $this->insert([
            'action'        => $action,
            'user_id'       => $actorId,
            'current'       => $current,
            'previous'      => $previous,
            'activity_date' => time(),
        ]);
    }

}
