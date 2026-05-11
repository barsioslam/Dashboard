<?php

$namespaces = [

    "Models\\"                => MODEL_PATH,
    "App\\Models\\"           => MODEL_PATH,
    "App\\Controllers\\"      => CONTROLLER_PATH,
    "App\\Views\\"            => VIEW_PATH,
    "App\\Views\\"            => VIEW_PATH   . "_layouts/",
    "App\\Utils\\"            => ASSETS_PATH . "php/utils/",
    "App\\Errors\\"           => ASSETS_PATH . "php/errors/",
    "App\\DB\\"               => ASSETS_PATH . "_db/"

];

spl_autoload_register(function ($class) use ($namespaces) {

    foreach ($namespaces as $prefix => $baseDir) {
        $len = strlen($prefix);

        // Vérifie si la classe commence par le namespace
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }

        // Récupère la partie après le namespace
        $relativeClass = substr($class, $len);

        // Transforme les "\" en "/" pour les sous-namespaces
        $file = $baseDir . str_replace("\\", "/", $relativeClass) . ".php";

        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }

});

function listLoadableClasses(array $namespaces): array {
    $classes = [];

    foreach ($namespaces as $prefix => $baseDir) {
        if (!is_dir($baseDir)) {
            continue;
        }

        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($baseDir));

        foreach ($rii as $file) {
            if ($file->isDir() || $file->getExtension() !== 'php') {
                continue;
            }

            // Récupère le chemin relatif au dossier de base
            $relativePath = substr($file->getPathname(), strlen($baseDir));
            $relativeClass = str_replace('/', '\\', substr($relativePath, 0, -4)); // -4 pour enlever ".php"

            $classes[] = $prefix . $relativeClass;
        }
    }

    return $classes;
}

?>