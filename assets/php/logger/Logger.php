<?php

namespace App\Logger;

use \DateTime;

class Logger {

    private static array $logs = [];
    private static string $logFile;

    public static function init(string $logFile = "access"): void {
        self::$logFile = LOGS_PATH . $logFile . '_' . date("d-m-Y") . '.txt';
    }

    public static function log(Level $level, string $message): array {
        $now = DateTime::createFromFormat('U.u', microtime(true));
        $date = $now->format("Y-m-d H:i:s.u");
        return [
            'timestamp' => $date,
            'level'     => $level->value,
            'message'   => $message
        ];
    }

    public static function info(string $message): array {
        $log = self::log(Level::INFO, $message);
        self::$logs[] = $log;
        return $log;
    }

    public static function success(string $message): array {
        $log = self::log(Level::SUCCESS, $message);
        self::$logs[] = $log;
        return $log;
    }

    public static function warn(string $message): array {
        $log = self::log(Level::WARNING, $message);
        self::$logs[] = $log;
        return $log;
    }

    public static function error(string $message): array {
        $log = self::log(Level::ERROR, $message);
        self::$logs[] = $log;
        return $log;
    }

    public static function getLogs(): array {
        return self::$logs;
    }

    public static function clearLogs(): void {
        self::$logs = [];
        if (!DEBUG) {
            file_put_contents(self::$logFile, "");
        }
    }
    
    public static function writeLogs(): void {
        foreach (self::$logs as $log) {
            $line = "[{$log['timestamp']}] [{$log['level']}] {$log['message']}";
            file_put_contents(self::$logFile, \str_replace('<br>', "\n", $line) . PHP_EOL, FILE_APPEND);
        }
    }
    
}
