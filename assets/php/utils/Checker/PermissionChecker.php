<?php

namespace App\Utils\Checker;

use App\Models\Permission;

class PermissionChecker {
    
    public static function has_perm($permission) : bool {
        $permissionModel = new Permission();
        $permission_id = $permissionModel->getIdByName($permission);
        if ($permission_id === null) {
            return false;
        }
        return has_perm_id($permission_id);
    }
    
    public static function has_perm_id($permission_id) : bool {
        return isset($_SESSION['user']['permissions']) AND in_array($permission_id, $_SESSION['user']['permissions']);
    }

}

?>