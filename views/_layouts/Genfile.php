<?php

namespace App\Views;

use App\Utils\Lang;

class Genfile {

    private Lang $lang;

    public function __construct(string $path, array $page, array $vars = []) {
        extract($vars);
        $this->lang = Lang::getInstance();
        $this->genheader($page, $vars);
        if (file_exists(VIEW_PATH . $path . '.php')) {
            require VIEW_PATH . $path . '.php';
        }
        $this->geneof($page);
    }

    private function genheader(array $page, array $vars = []) {
        $lang = $this->lang;
        extract($vars);
        require_once VIEW_PATH . '_layouts/header.php';
    }

    private function geneof(array $page) {
        $lang = $this->lang;
        require_once VIEW_PATH . '_layouts/eof.php';
    }

}

?>