<?php

session_start();

define('DEBUG', true);

if (DEBUG) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(0);
}

// Définition des constantes de chemin

define('MODEL_PATH', __DIR__ . '/models/');
define('VIEW_PATH', __DIR__ . '/views/');
define('CONTROLLER_PATH', __DIR__ . '/controllers/');

define('LAYOUT_PATH', VIEW_PATH . '_layouts/');

define('ASSETS_PATH', __DIR__ . '/assets/');
define('LANG_PATH', ASSETS_PATH . '_lang/');
define('CONF_PATH', ASSETS_PATH . '_conf/');
define('UPLOAD_PATH', ASSETS_PATH . '_uploads/');

// Chargement automatique des classes

$coreClasses = get_declared_classes();

require_once(ASSETS_PATH . 'php/AutoLoad.php');

// Importation des classes nécessaires

use App\Utils\JSONReader;
use App\Utils\Lang;
use App\Utils\Misc;

use App\Controllers\HomeController;
use App\Controllers\ErrorController;
use App\Controllers\AccountController;
use App\Controllers\DashboardController;

use App\Errors\Error401;
use App\Errors\Error403;
use App\Errors\Error404;
use App\Errors\Error500;

// Analyse de l'URL

$url = isset($_GET['url']) ? $_GET['url'] : 'home/index';
$url = rtrim($url, '/');
$segments = explode('/', $url);

// Gestion de la langue

if (isset($_GET['lang'])) {
    if (Lang::hasLang($_GET['lang']) === false) {
        $urlPart = $_GET['url'] ?? '';
        if ($urlPart == 'index.php') $urlPart = '';
        header('Location: /' . $_SESSION['lang'] . '/' . $urlPart);
        exit;
    } else {
        $langCode = $_GET['lang'];
    }
    $_SESSION['lang'] = $langCode;
} elseif (isset($_SESSION['lang'])) {

    $langCode = $_SESSION['lang'];

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $urlPart = $_GET['url'] ?? '';

        if ($urlPart == 'index.php') {
            $urlPart = '';
        }

        $query = $_GET;
        unset($query['url'], $query['lang']);

        $queryString = http_build_query($query);

        $redirect = '/' . $langCode . '/' . $urlPart;

        if (!empty($queryString)) {
            $redirect .= '?' . $queryString;
        }

        header('Location: ' . $redirect);
        exit;
    }

} else {
    $langCode = 'en'; // langue par défaut
    $_SESSION['lang'] = $langCode;
}

Lang::init($langCode);

$controllerSegment = preg_replace('/[^a-zA-Z0-9]/', '', $segments[0]);
$controllerName = "App\\Controllers\\" . ucfirst($controllerSegment) . "Controller";
$method = preg_replace('/[^a-zA-Z0-9_]/', '', $segments[1] ?? 'index');
$params = array_slice($segments, 2);

$trigger404 = function (string $reason): void {
    http_response_code(404);
    $errcontroller = new ErrorController();
    $errcontroller->error404($reason);
};

if (str_starts_with($method, '_')) {
    $trigger404(Error404::PAGE_NOT_FOUND);
} elseif (class_exists($controllerName)) {
    $controller = new $controllerName();

    if (method_exists($controller, $method)) {
        call_user_func_array([$controller, $method], $params);
    } else {
        $trigger404(Error404::PAGE_NOT_FOUND);
    }
} else {
    $controller = new HomeController();
    $fallbackMethod = preg_replace('/[^a-zA-Z0-9_]/', '', $segments[0]);

    if (!str_starts_with($fallbackMethod, '_') && method_exists($controller, $fallbackMethod)) {
        call_user_func_array([$controller, $fallbackMethod], $params);
    } else {
        $trigger404(Error404::CONTROLLER_NOT_FOUND);
    }
}

?>