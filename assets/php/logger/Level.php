<?php

namespace App\Logger;

enum Level: string {

    case INFO    = 'INFO';
    case SUCCESS = 'SUCCESS';
    case WARNING = 'WARNING';
    case ERROR   = 'ERROR';
    
}

?>