<?php

namespace App\Utils\System;

class SystemStats {

    public static function get(): array {
        $stats = ['cpu' => 0, 'ram' => 0, 'disk' => 0, 'net' => 0];

        // Disque (cross-platform, PHP natif)
        $diskPath  = PHP_OS_FAMILY === 'Windows' ? 'C:' : '/';
        $diskFree  = @disk_free_space($diskPath) ?: 0;
        $diskTotal = @disk_total_space($diskPath) ?: 0;
        if ($diskTotal > 0) {
            $stats['disk'] = (int) round((1 - $diskFree / $diskTotal) * 100);
        }

        if (PHP_OS_FAMILY === 'Linux') {
            self::fillLinux($stats);
        } elseif (PHP_OS_FAMILY === 'Windows') {
            self::fillWindows($stats);
        }

        return $stats;
    }

    private static function fillLinux(array &$stats): void {
        // RAM via /proc/meminfo
        if (is_readable('/proc/meminfo')) {
            $mem = file_get_contents('/proc/meminfo');
            preg_match('/MemTotal:\s+(\d+)/', $mem, $t);
            preg_match('/MemAvailable:\s+(\d+)/', $mem, $a);
            if ($t && $a && (int) $t[1] > 0) {
                $stats['ram'] = (int) round((1 - (int) $a[1] / (int) $t[1]) * 100);
            }
        }

        // CPU via load average (non-bloquant — 1 min average / nb de cœurs)
        $load = @sys_getloadavg();
        if ($load !== false) {
            $cores = max(1, (int) @shell_exec('nproc 2>/dev/null'));
            $stats['cpu'] = (int) min(round($load[0] / $cores * 100), 100);
        }

        // Réseau : débit entrant sur la première interface active (octets/s sur ~100 ms)
        $stats['net'] = self::networkLinux();
    }

    private static function fillWindows(array &$stats): void {
        // RAM via wmic
        $out = [];
        @exec('wmic OS get TotalVisibleMemorySize,FreePhysicalMemory /format:list 2>nul', $out);
        $data = [];
        foreach ($out as $line) {
            if (str_contains($line, '=')) {
                [$k, $v] = explode('=', $line, 2);
                $data[trim($k)] = (int) trim($v);
            }
        }
        if (!empty($data['TotalVisibleMemorySize']) && $data['TotalVisibleMemorySize'] > 0) {
            $stats['ram'] = (int) round(
                (1 - ($data['FreePhysicalMemory'] ?? 0) / $data['TotalVisibleMemorySize']) * 100
            );
        }

        // CPU via wmic
        $out = [];
        @exec('wmic cpu get loadpercentage /value 2>nul', $out);
        foreach ($out as $line) {
            if (str_contains($line, '=')) {
                [, $v] = explode('=', $line, 2);
                $stats['cpu'] = (int) trim($v);
                break;
            }
        }

        // Réseau : non disponible sans WMI étendu, laissé à 0
    }

    private static function networkLinux(): int {
        if (!is_readable('/proc/net/dev')) return 0;

        $parse = static function (string $content): array {
            $totals = [];
            foreach (explode("\n", $content) as $line) {
                $line = trim($line);
                // Ignore lo et les en-têtes
                if (!$line || str_starts_with($line, 'Inter') || str_starts_with($line, 'face')) continue;
                [$iface, $rest] = explode(':', $line, 2);
                $iface = trim($iface);
                if ($iface === 'lo') continue;
                $cols = preg_split('/\s+/', trim($rest));
                $totals[$iface] = (int) $cols[0] + (int) ($cols[8] ?? 0); // rx_bytes + tx_bytes
            }
            return $totals;
        };

        $t1 = $parse(file_get_contents('/proc/net/dev'));
        usleep(100000); // 100 ms
        $t2 = $parse(file_get_contents('/proc/net/dev'));

        $bytesPerSec = 0;
        foreach ($t2 as $iface => $val) {
            $bytesPerSec += max(0, $val - ($t1[$iface] ?? $val)) * 10;
        }

        // Exprime en % d'un lien 1 Gbps (125 Mo/s)
        return (int) min(round($bytesPerSec / (125 * 1024 * 1024) * 100), 100);
    }
}
