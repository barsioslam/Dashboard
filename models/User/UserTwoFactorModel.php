<?php

namespace Models\User;

use Models\Model;

class UserTwoFactorModel extends Model {

    protected string $table = 'user_two_factor';

    // -------------------------------------------------------
    //  Lecture
    // -------------------------------------------------------

    public function getByUserId(int $userId): ?array {
        $result = $this->getBy('user_id', $userId);
        return $result[0] ?? null;
    }

    /**
     * Retourne l'entrée user_two_factor + son app fusionnés,
     * ou null si aucune méthode TOTP n'est configurée.
     */
    public function getApp(int $userId): ?array {
        $row = $this->getByUserId($userId);
        if ($row === null) return null;

        $app = (new UserTwoFactorAppModel())->getById((int) $row['id']);
        if ($app === null) return null;

        return array_merge($row, $app);
    }

    public function hasApp(int $userId): bool {
        return $this->getApp($userId) !== null;
    }

    // -------------------------------------------------------
    //  Écriture
    // -------------------------------------------------------

    /**
     * Crée la ligne parent dans user_two_factor et retourne son id.
     */
    private function createEntry(int $userId, string $name): int {
        return $this->insert(['user_id' => $userId, 'name' => $name]);
    }

    /**
     * Crée la méthode TOTP (user_two_factor + user_two_factor_app).
     */
    public function createApp(int $userId, string $secret, string $name = 'authenticator'): void {
        $id = $this->createEntry($userId, $name);
        (new UserTwoFactorAppModel())->create($id, $secret);
    }

    /**
     * Supprime la méthode TOTP (app d'abord, puis entrée parente).
     * La FK ON DELETE CASCADE assure la cohérence si la suppression directe suffit,
     * mais on reste explicite ici.
     */
    public function deleteApp(int $userId): void {
        $row = $this->getByUserId($userId);
        if ($row === null) return;

        $id = (int) $row['id'];
        (new UserTwoFactorAppModel())->delete($id);
        $this->delete($id);
    }

}