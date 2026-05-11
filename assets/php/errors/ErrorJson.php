<?php

namespace App\Errors;

enum ErrorJson: string {

    case FILE_NOT_FOUND       = 'FILE_NOT_FOUND';
    case INVALID_JSON         = 'INVALID_JSON';
    case PATH_NOT_FOUND       = 'PATH_NOT_FOUND';
    case UNKNOWN_ERROR        = 'UNKNOWN_ERROR';

    public function message(): string {
        return match($this) {
            self::FILE_NOT_FOUND => '$lang::error/json/message/file_not_found',
            self::INVALID_JSON => '$lang::error/json/message/invalid_json',
            self::PATH_NOT_FOUND => '$lang::error/json/message/path_not_found',
            self::UNKNOWN_ERROR => '$lang::error/json/message/unknown_error'
        };
    }
    
}

?>