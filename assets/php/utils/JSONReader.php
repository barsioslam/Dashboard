<?php

namespace App\Utils;

use Exception;

class JSONReader {
    private $data;

    public function __construct($filePath) {
        if (!file_exists($filePath)) {
            throw new Exception("File not found: " . $filePath);
        }

        $jsonContent = file_get_contents($filePath);
        $this->data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error decoding JSON from file: " . $filePath);
        }
    }

    function getByPath($path, $default = null) {
        $keys = explode('/', $path);
        $temp = $this->data;
        foreach ($keys as $key) {
            if (isset($temp[$key])) {
                $temp = $temp[$key];
            } else {
                return null; // chemin invalide
            }
        }
        if ($temp === null OR $temp == "") {
            return $default;
        }
        return $temp;
    }

    public function get($key, $default = null) {
        if ($this->data[$key] === null OR $this->data[$key] == "") {
            return $default;
        }
        return $this->data[$key];
    }

    public function getAll() {
        return $this->data;
    }
}

?>