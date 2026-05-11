<?php

// ErrorController.php

namespace App\Controllers;

use App\Views\Genfile;

use App\Errors\Error401;
use App\Errors\Error403;
use App\Errors\Error404;
use App\Errors\Error500;

class ErrorController {

    public function error401($errtype = Error401::UNAUTHORIZED) {
        $page['title'] = '$lang::error/401/title';
        $page['description'] = '$lang::error/401/description';
        $page['csslist'] = [];
        $page['jspreloadlist'] = [];
        $page['jspostloadlist'] = [];
        new Genfile('error/error401', $page);
    }

    public function error403($errtype = Error403::UNAUTHORIZED) {
        $page['title'] = '$lang::error/403/title';
        $page['description'] = '$lang::error/403/description';
        $page['csslist'] = [];
        $page['jspreloadlist'] = [];
        $page['jspostloadlist'] = [];
        new Genfile('error/error403', $page);
    }

    public function error404($errtype = Error404::PAGE_NOT_FOUND) {
        $page['title'] = '$lang::error/404/title';
        $page['description'] = '$lang::error/404/description';
        $page['csslist'] = [];
        $page['jspreloadlist'] = [];
        $page['jspostloadlist'] = [];
        new Genfile('error/error404', $page);
    }

    public function error500($errtype = Error500::SERVER_ERROR) {
        $page['title'] = '$lang::error/500/title';
        $page['description'] = '$lang::error/500/description';
        $page['csslist'] = [];
        $page['jspreloadlist'] = [];
        $page['jspostloadlist'] = [];
        new Genfile('error/error500', $page);
    }

}

?>