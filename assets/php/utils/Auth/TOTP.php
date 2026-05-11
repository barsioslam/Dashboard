<?php

namespace App\Utils\Auth;

class TOTP {

    private const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    private const STEP     = 30;
    private const DIGITS   = 6;

    public static function generateSecret(): string {
        return self::base32Encode(random_bytes(20));
    }

    public static function verify(string $secret, string $code, int $window = 1): bool {
        $code = preg_replace('/\s+/', '', $code);
        if (strlen($code) !== self::DIGITS || !ctype_digit($code)) {
            return false;
        }
        $counter = (int) floor(time() / self::STEP);
        for ($i = -$window; $i <= $window; $i++) {
            if (hash_equals(self::hotp($secret, $counter + $i), $code)) {
                return true;
            }
        }
        return false;
    }

    public static function getOtpAuthUri(string $secret, string $label, string $issuer = 'TaderLafe'): string {
        return 'otpauth://totp/' . rawurlencode($issuer . ':' . $label)
             . '?secret='  . $secret
             . '&issuer='  . rawurlencode($issuer)
             . '&algorithm=SHA1&digits=6&period=30';
    }

    private static function hotp(string $secret, int $counter): string {
        $key    = self::base32Decode($secret);
        $msg    = pack('N', 0) . pack('N', $counter);
        $hmac   = hash_hmac('sha1', $msg, $key, true);
        $offset = ord($hmac[19]) & 0x0F;
        $code   = (
            (ord($hmac[$offset])     & 0x7F) << 24 |
            (ord($hmac[$offset + 1]) & 0xFF) << 16 |
            (ord($hmac[$offset + 2]) & 0xFF) << 8  |
            (ord($hmac[$offset + 3]) & 0xFF)
        ) % (10 ** self::DIGITS);
        return str_pad((string) $code, self::DIGITS, '0', STR_PAD_LEFT);
    }

    private static function base32Encode(string $data): string {
        $output   = '';
        $buffer   = 0;
        $bitsLeft = 0;
        foreach (str_split($data) as $byte) {
            $buffer    = ($buffer << 8) | ord($byte);
            $bitsLeft += 8;
            while ($bitsLeft >= 5) {
                $output   .= self::ALPHABET[($buffer >> ($bitsLeft - 5)) & 0x1F];
                $bitsLeft -= 5;
            }
        }
        if ($bitsLeft > 0) {
            $output .= self::ALPHABET[($buffer << (5 - $bitsLeft)) & 0x1F];
        }
        return $output;
    }

    private static function base32Decode(string $b32): string {
        $b32      = strtoupper(rtrim($b32, '='));
        $output   = '';
        $buffer   = 0;
        $bitsLeft = 0;
        foreach (str_split($b32) as $char) {
            $pos = strpos(self::ALPHABET, $char);
            if ($pos === false) continue;
            $buffer    = ($buffer << 5) | $pos;
            $bitsLeft += 5;
            if ($bitsLeft >= 8) {
                $output   .= chr(($buffer >> ($bitsLeft - 8)) & 0xFF);
                $bitsLeft -= 8;
            }
        }
        return $output;
    }
}
