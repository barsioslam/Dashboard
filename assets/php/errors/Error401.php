<?php

namespace App\Errors;

enum Error401: string {

    case UNAUTHORIZED         = 'UNAUTHORIZED';
    case FORBIDDEN            = 'FORBIDDEN';
    case UNKNOWN_ERROR        = 'UNKNOWN_ERROR';

    public function message(): string {
        return match($this) {
            self::UNAUTHORIZED => '$lang::error/403/message/unauthorized',
            self::FORBIDDEN => '$lang::error/403/message/forbidden',
            self::UNKNOWN_ERROR => '$lang::error/500/message/unknown_error'
        };
    }

}

?>