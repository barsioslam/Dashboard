<?php

namespace App\Utils\Checker;

use Models\User\UserSessionModel;
use App\Utils\SessionManager;

class AccountChecker {

    private static ?bool $cache = null;

    public static function logged(): bool {
        if (self::$cache !== null) {
            return self::$cache;
        }

        if (!isset($_SESSION['user_id'])) {
            return self::$cache = false;
        }

        // Sessions créées avant le système de tracking : on les laisse passer
        if (!isset($_SESSION['session_token'])) {
            return self::$cache = true;
        }

        $model = new UserSessionModel();
        if (!$model->isActive($_SESSION['session_token'])) {
            session_destroy();
            return self::$cache = false;
        }

        SessionManager::touch();

        return self::$cache = true;
    }

    public static function loginUrl(): string {
        $lang = $_SESSION['lang'] ?? 'en';
        return '/' . $lang . '/auth/login';
    }

    public static function isAdmin(): bool {
        return true;
    }

}

?>