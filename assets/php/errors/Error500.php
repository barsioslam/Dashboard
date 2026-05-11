<?php

namespace App\Errors;

enum Error500: string {

    case SERVER_ERROR         = 'SERVER_ERROR';
    case BAD_REQUEST          = 'BAD_REQUEST';
    case VALIDATION_ERROR     = 'VALIDATION_ERROR';
    case DATABASE_ERROR       = 'DATABASE_ERROR';
    case UNKNOWN_ERROR        = 'UNKNOWN_ERROR';

    public function message(): string {
        return match($this) {
            self::SERVER_ERROR => '$lang::error/500/message/server_error',
            self::BAD_REQUEST => '$lang::error/500/message/bad_request',
            self::VALIDATION_ERROR => '$lang::error/500/message/validation_error',
            self::DATABASE_ERROR => '$lang::error/500/message/database_error',
            self::UNKNOWN_ERROR => '$lang::error/500/message/unknown_error'
        };
    }

}

?>