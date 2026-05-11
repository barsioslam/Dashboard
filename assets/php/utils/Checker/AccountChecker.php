<?php

namespace App\Utils\Checker;

class AccountChecker {
    
    public static function logged() : bool {
        return isset($_SESSION['user_id']);
    }

    public static function isAdmin() : bool {
        return true;
    }

}

?>