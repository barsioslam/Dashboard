<?php

namespace App\Errors;

enum Error404: string {

    case CONTROLLER_NOT_FOUND = 'CONTROLLER_NOT_FOUND';
    case ACTION_NOT_FOUND     = 'ACTION_NOT_FOUND';
    case PAGE_NOT_FOUND       = 'PAGE_NOT_FOUND';
    case INVALID_LANGUAGE     = 'INVALID_LANGUAGE';
    case UNKNOWN_ERROR        = 'UNKNOWN_ERROR';

    public function message(): string {
        return match($this) {
            self::CONTROLLER_NOT_FOUND => '$lang::error/404/message/controller_not_found',
            self::ACTION_NOT_FOUND => '$lang::error/404/message/action_not_found',
            self::PAGE_NOT_FOUND => '$lang::error/404/message/page_not_found',
            self::INVALID_LANGUAGE => '$lang::error/404/message/invalid_language',
            self::UNKNOWN_ERROR => '$lang::error/500/message/unknown_error'
        };
    }

}

?>