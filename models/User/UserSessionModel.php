<?php

namespace Models\User;

use Models\Model;

class UserSessionModel extends Model {

    protected string $table      = 'user_session';
    protected string $primaryKey = 'token';

    public function createSession(string $token, int $userId, string $ip, array $parsed, string $ua): void {
        $now = time();
        $this->db->query(
            'INSERT INTO `user_session`
                (`token`, `user_id`, `ip`, `country`, `browser`, `os`, `device`, `user_agent`, `created_at`, `last_activity`)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE `last_activity` = ?',
            [
                $token,
                $userId,
                $ip,
                $parsed['country'] ?? null,
                $parsed['browser'] ?? '',
                $parsed['os']      ?? '',
                $parsed['device']  ?? 'Desktop',
                $ua,
                $now,
                $now,
                $now,
            ]
        );
    }

    public function isActive(string $token): bool {
        $this->db->query(
            'SELECT 1 FROM `user_session` WHERE `token` = ? LIMIT 1',
            [$token]
        );
        return !empty($this->db->fetchAll());
    }

    public function touch(string $token): void {
        $this->db->query(
            'UPDATE `user_session` SET `last_activity` = ? WHERE `token` = ?',
            [time(), $token]
        );
    }

    public function getByUser(int $userId): array {
        $this->db->query(
            'SELECT * FROM `user_session` WHERE `user_id` = ? ORDER BY `last_activity` DESC',
            [$userId]
        );
        return $this->db->fetchAll();
    }

    public function revoke(string $token): bool {
        $this->db->query(
            'DELETE FROM `user_session` WHERE `token` = ?',
            [$token]
        );
        return $this->db->rowCount() > 0;
    }

    public function revokeForUser(string $token, int $userId): bool {
        $this->db->query(
            'DELETE FROM `user_session` WHERE `token` = ? AND `user_id` = ?',
            [$token, $userId]
        );
        return $this->db->rowCount() > 0;
    }

    public function revokeAllExcept(string $currentToken, int $userId): int {
        $this->db->query(
            'DELETE FROM `user_session` WHERE `user_id` = ? AND `token` != ?',
            [$userId, $currentToken]
        );
        return $this->db->rowCount();
    }

    public function revokeAll(int $userId): int {
        $this->db->query(
            'DELETE FROM `user_session` WHERE `user_id` = ?',
            [$userId]
        );
        return $this->db->rowCount();
    }

    public function purgeExpired(int $maxAge = 2592000): int {
        $this->db->query(
            'DELETE FROM `user_session` WHERE `last_activity` < ?',
            [time() - $maxAge]
        );
        return $this->db->rowCount();
    }

}
