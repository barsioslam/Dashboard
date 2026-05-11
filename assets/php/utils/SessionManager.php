<?php

namespace App\Utils;

use Models\User\UserSessionModel;

class SessionManager {

    public static function start(int $userId): void {
        session_regenerate_id(true);

        $token  = session_id();
        $ua     = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $ip     = self::getClientIP();
        $parsed = self::parseUA($ua);
        $parsed['country'] = self::getCountry($ip);

        $_SESSION['session_token'] = $token;

        (new UserSessionModel())->createSession($token, $userId, $ip, $parsed, $ua);
    }

    public static function touch(): void {
        $token = $_SESSION['session_token'] ?? null;
        if (!$token) {
            return;
        }
        // Limite les écritures : 1 UPDATE max toutes les 5 minutes
        $now = time();
        if (!isset($_SESSION['_last_touch']) || $now - $_SESSION['_last_touch'] > 300) {
            (new UserSessionModel())->touch($token);
            $_SESSION['_last_touch'] = $now;
        }
    }

    public static function destroy(): void {
        $token = $_SESSION['session_token'] ?? null;
        if ($token) {
            (new UserSessionModel())->revoke($token);
        }
    }

    // -------------------------------------------------------

    public static function getClientIP(): string {
        $candidates = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'REMOTE_ADDR',
        ];
        foreach ($candidates as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = trim(explode(',', $_SERVER[$key])[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        return '0.0.0.0';
    }

    public static function getCountry(string $ip): ?string {
        // Brancher ici MaxMind GeoIP2 ou geoip extension PHP
        // Exemple : return (new \GeoIp2\Database\Reader('/path/to/GeoLite2-Country.mmdb'))->country($ip)->country->name;
        if (function_exists('geoip_country_name_by_name')) {
            $name = @geoip_country_name_by_name($ip);
            return $name ?: null;
        }
        return null;
    }

    public static function parseUA(string $ua): array {
        return [
            'browser' => self::parseBrowser($ua),
            'os'      => self::parseOS($ua),
            'device'  => self::parseDevice($ua),
        ];
    }

    // -------------------------------------------------------

    private static function parseBrowser(string $ua): string {
        if (str_contains($ua, 'OPR/') || str_contains($ua, 'Opera/')) {
            return 'Opera';
        }
        if (str_contains($ua, 'Edg/') || str_contains($ua, 'Edge/')) {
            return 'Edge';
        }
        if (str_contains($ua, 'SamsungBrowser/')) {
            return 'Samsung Internet';
        }
        if (str_contains($ua, 'Chrome/')) {
            return 'Chrome';
        }
        if (str_contains($ua, 'Firefox/') || str_contains($ua, 'FxiOS/')) {
            return 'Firefox';
        }
        if (str_contains($ua, 'Safari/')) {
            return 'Safari';
        }
        if (str_contains($ua, 'Trident/') || str_contains($ua, 'MSIE ')) {
            return 'Internet Explorer';
        }
        return 'Inconnu';
    }

    private static function parseOS(string $ua): string {
        if (str_contains($ua, 'iPhone')) {
            preg_match('/CPU iPhone OS ([\d_]+)/', $ua, $m);
            $v = isset($m[1]) ? str_replace('_', '.', $m[1]) : '';
            return 'iOS' . ($v ? ' ' . $v : '');
        }
        if (str_contains($ua, 'iPad')) {
            preg_match('/CPU OS ([\d_]+)/', $ua, $m);
            $v = isset($m[1]) ? str_replace('_', '.', $m[1]) : '';
            return 'iPadOS' . ($v ? ' ' . $v : '');
        }
        if (str_contains($ua, 'Android')) {
            preg_match('/Android ([\d.]+)/', $ua, $m);
            return 'Android' . (isset($m[1]) ? ' ' . $m[1] : '');
        }
        if (str_contains($ua, 'Windows NT')) {
            $map = ['10.0' => '10/11', '6.3' => '8.1', '6.2' => '8', '6.1' => '7', '6.0' => 'Vista', '5.1' => 'XP'];
            preg_match('/Windows NT ([\d.]+)/', $ua, $m);
            $v = $map[$m[1] ?? ''] ?? ($m[1] ?? '');
            return 'Windows ' . $v;
        }
        if (str_contains($ua, 'Macintosh') || str_contains($ua, 'Mac OS X')) {
            preg_match('/Mac OS X ([\d_]+)/', $ua, $m);
            $v = isset($m[1]) ? str_replace('_', '.', $m[1]) : '';
            return 'macOS' . ($v ? ' ' . $v : '');
        }
        if (str_contains($ua, 'Linux')) {
            return 'Linux';
        }
        if (str_contains($ua, 'CrOS')) {
            return 'ChromeOS';
        }
        return 'Inconnu';
    }

    private static function parseDevice(string $ua): string {
        if (str_contains($ua, 'iPhone')) {
            return 'Mobile';
        }
        if (str_contains($ua, 'iPad')) {
            return 'Tablette';
        }
        if (str_contains($ua, 'Android')) {
            return str_contains($ua, 'Mobile') ? 'Mobile' : 'Tablette';
        }
        return 'Desktop';
    }

}
