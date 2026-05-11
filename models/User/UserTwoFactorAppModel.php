<?php

namespace Models\User;

use Models\Model;

class UserTwoFactorAppModel extends Model {

    protected string $table = 'user_two_factor_app';

    /**
     * L'id n'est pas auto-increment : c'est un FK partagé avec user_two_factor.
     * On ne peut pas utiliser insert() standard car il faudrait spécifier l'id.
     */
    public function create(int $id, string $secret): void {
        $this->db->query(
            "INSERT INTO `user_two_factor_app` (`id`, `secret`) VALUES (?, ?)",
            [$id, $secret]
        );
    }

    // getById(int $id)  → hérité de Model
    // delete(int $id)   → hérité de Model

}