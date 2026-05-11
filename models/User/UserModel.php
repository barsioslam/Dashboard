<?php

namespace Models\User;

use Models\Model;
use Models\User\UserTwoFactorModel;

class UserModel extends Model {

    protected string $table = 'user';

    public function register(array $data): int {
        $data['password']       = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['created_at']     = time();
        $data['is_active']      = $data['is_active'] ?? true;
        $data['email_verified'] = $data['email_verified'] ?? false;
        return $this->insert($data);
    }

    public function login(string $identifier, string $password): ?array {
        $this->db->query(
            "SELECT * FROM `user` WHERE `email` = ? OR `username` = ? LIMIT 1",
            [$identifier, $identifier]
        );
        $result = $this->db->fetchAll();
        if (empty($result)) {
            return null;
        }
        $user = $result[0];
        if (!password_verify($password, $user['password'])) {
            return null;
        }
        return $user;
    }

    public function findById(int $id): ?array {
        $result = $this->getBy('id', $id);
        return $result[0] ?? null;
    }

    public function findByEmail(string $email): ?array {
        $result = $this->getBy('email', $email);
        return $result[0] ?? null;
    }

    public function findByUsername(string $username): ?array {
        $result = $this->getBy('username', $username);
        return $result[0] ?? null;
    }

    public function emailExists(string $email): bool {
        return $this->countBy('email', $email) > 0;
    }

    public function usernameExists(string $username): bool {
        return $this->countBy('username', $username) > 0;
    }

    public function updatePassword(int $id, string $newPassword): int {
        return $this->update($id, ['password' => password_hash($newPassword, PASSWORD_DEFAULT)]);
    }

    public function setActive(int $id, bool $active): int {
        return $this->update($id, ['is_active' => $active]);
    }

    public function verifyEmail(int $id): int {
        return $this->update($id, ['email_verified' => true]);
    }

    public function enable2FA(int $userId, string $secret): void {
        (new UserTwoFactorModel())->createApp($userId, $secret);
    }

    public function disable2FA(int $userId): void {
        (new UserTwoFactorModel())->deleteApp($userId);
    }

    public function get2FAApp(int $userId): ?array {
        return (new UserTwoFactorModel())->getApp($userId);
    }

    public function has2FAApp(int $userId): bool {
        return (new UserTwoFactorModel())->hasApp($userId);
    }

    public function countActive(): int {
        return $this->countBy('is_active', 1);
    }

    public function countNewSince(int $since): int {
        $this->db->query('SELECT COUNT(*) AS total FROM `user` WHERE `created_at` >= ?', [$since]);
        $result = $this->db->fetchAll();
        return (int) ($result[0]['total'] ?? 0);
    }

    public function getRecentWithRole(int $limit = 5): array {
        $this->db->query(
            'SELECT u.id, u.username, u.email, u.first_name, u.last_name, u.created_at,
                    r.name AS role_name, r.color AS role_color
             FROM `user` u
             LEFT JOIN user_role ur ON ur.id_user = u.id
             LEFT JOIN role r ON r.id = ur.id_role
             WHERE u.is_active = 1
             ORDER BY u.created_at DESC
             LIMIT ' . (int) $limit
        );
        return $this->db->fetchAll();
    }

}