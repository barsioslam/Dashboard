<?php

declare(strict_types=1);

namespace App\Utils;

use Exception;

class Lang
{
    private static ?Lang $instance = null;
    private string $locale;
    private JSONReader $langfile;

    private function __construct(string $locale)
    {
        $this->locale = $locale;

        $filePath = rtrim(LANG_PATH, '/\\') . DIRECTORY_SEPARATOR . $locale . DIRECTORY_SEPARATOR . 'strings.json';

        if (!file_exists($filePath)) {
            throw new Exception("Language file not found: " . $filePath);
        }

        $this->langfile = new JSONReader($filePath);
    }

    public static function init(string $locale): void
    {
        if (!self::$instance) {
            self::$instance = new self($locale);
        }
    }

    public static function getInstance(): Lang
    {
        if (!self::$instance) {
            throw new Exception("Lang is not initialized. Call Lang::init(\$locale) first.");
        }

        return self::$instance;
    }

    public static function hasLang(string $langCode): bool
    {
        $filePath = rtrim(LANG_PATH, '/\\') . DIRECTORY_SEPARATOR . $langCode . DIRECTORY_SEPARATOR . 'strings.json';
        return file_exists($filePath);
    }

    public function getByPath(string $path, $default = "[MISSING STRING]")
    {
        return $this->langfile->getByPath($path, $default);
    }

    public function get(string $key, $default = null)
    {
        return $this->langfile->get($key, $default);
    }

    public function getAll(): array
    {
        return $this->langfile->getAll();
    }

    public function getLocale(): string
    {
        return $this->locale;
    }
}
