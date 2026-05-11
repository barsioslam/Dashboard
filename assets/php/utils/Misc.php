<?php

namespace App\Utils;

abstract class Misc {

    public static function isList(array $array) : bool {
        return array_keys($array) === range(0, count($array) - 1);
    }

    public static function isDict(array $array) : bool {
        return !self::isList($array);
    }

    public static function getClientIp(bool $onlyValidated = true): string {
        // Ordre de confiance (adapter selon ton infra — ex : si tu utilises Cloudflare ou un load balancer)
        $keys = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',   // peut contenir une liste d'IPs
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR',
            // Cloudflare / autres proxies spécifiques :
            'HTTP_CF_CONNECTING_IP',
            'HTTP_TRUE_CLIENT_IP'
        ];

        foreach ($keys as $key) {
            if (!empty($_SERVER[$key])) {
                $ipList = $_SERVER[$key];

                // si header contient une liste d'IPs (X-Forwarded-For), on prend la première IP non privée/public si possible
                if (strpos($ipList, ',') !== false) {
                    $ips = array_map('trim', explode(',', $ipList));
                } else {
                    $ips = [trim($ipList)];
                }

                foreach ($ips as $ip) {
                    // optionnel : valider l'IP (évite les injections)
                    if (!$onlyValidated || filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                        // retourne la première ip publique valide
                        return $ip;
                    }

                    // si on accepte aussi les IP privées/réservées (ex: réseau interne), enlever les flags ci-dessus
                    if (!$onlyValidated && filter_var($ip, FILTER_VALIDATE_IP)) {
                        return $ip;
                    }
                }
            }
        }

        return "0.0.0.0"; // ip introuvable
    }

    public static function getClientInfos(): string {
        $userAgent   = $_SERVER['HTTP_USER_AGENT']     ?? 'inconnu';

        // petit parsing du User-Agent
        $browser = 'Inconnu';
        $os = 'Inconnu';

        if (preg_match('/linux/i', $userAgent)) {
            $os = 'Linux';
        } elseif (preg_match('/macintosh|mac os x/i', $userAgent)) {
            $os = 'Mac';
        } elseif (preg_match('/windows|win32/i', $userAgent)) {
            $os = 'Windows';
        }

        if (preg_match('/MSIE/i',$userAgent) && !preg_match('/Opera/i',$userAgent)) {
            $browser = 'Internet Explorer';
        } elseif (preg_match('/Firefox/i',$userAgent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/Chrome/i',$userAgent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/Safari/i',$userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/Opera/i',$userAgent)) {
            $browser = 'Opera';
        } elseif (preg_match('/Netscape/i',$userAgent)) {
            $browser = 'Netscape';
        }

        return "OS: $os | Navigateur: $browser | UA: $userAgent";
    }

}

?>