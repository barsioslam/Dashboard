<?php

namespace App\Utils\Text;

use App\Utils\Lang;

class Text {

    public static function write(string $text) : string {
        if (str_starts_with($text, '$lang::')) {
            $text = str_replace('$lang::', '', $text);
            return self::writeLang($text);
        }
        return $text;
    }

    public static function writeLang(string $text) : string {
        $lang = Lang::getInstance();
        $return = $lang->getByPath($text);
        if ($return === null || $return == "") {
            $return = "[MISSING STRING]";
        }
        return $return;
    }

}

?>