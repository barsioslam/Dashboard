<?php

// AuthController.php

namespace App\Controllers;

use App\Views\Genfile;

use App\Logger\Logger;

use Models\User\UserModel;

use App\Utils\Auth\TOTP;
use App\Utils\Checker\FormChecker;
use App\Utils\Checker\AccountChecker;
use App\Utils\SessionManager;

class AuthController {

    public function login() {
        if (AccountChecker::logged()) {
            header("Location: /home/");
            exit();
        }

        $messages = [];
        if (FormChecker::formUploaded()) {
            $identifier = trim($_POST['username'] ?? '');
            $password   = $_POST['password'] ?? '';
            $userModel  = new UserModel();
            $user       = $userModel->login($identifier, $password);
            if ($user === null) {
                $messages['general'][] = '$lang::forms/incorrect';
            } elseif (!(bool) $user['is_active']) {
                $messages['general'][] = '$lang::auth/login/account_disabled';
            } elseif ($userModel->has2FAApp((int) $user['id'])) {
                $_SESSION['2fa_pending_user_id'] = (int) $user['id'];
                header('Location: /auth/TFA');
                exit;
            } else {
                $_SESSION['user_id']    = $user['id'];
                $_SESSION['username']   = $user['username'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name']  = $user['last_name'];
                $_SESSION['email']      = $user['email'];
                SessionManager::start((int) $user['id']);
                header('Location: /home/index');
                exit;
            }
        }

        $page['title']          = '$lang::auth/login/title';
        $page['description']    = '$lang::auth/login/description';
        $page['csslist']        = ['auth/login'];
        $page['jspreloadlist']  = [];
        $page['jspostloadlist'] = [];
        new Genfile('auth/login', $page, ['messages' => $messages]);
    }

    public function signin() {
        if (AccountChecker::logged()) {
            if (AccountChecker::isAdmin()) {
                header('Location: /dashboard/');
            } else {
                header('Location: /account/me');
            }
            exit();
        }

        $messages = [];
        if (FormChecker::formUploaded()) {
            $set = ['username', 'lastname', 'firstname', 'email', 'password', 'repassword'];
            $checker = new FormChecker($set);
            $checker->setRules([
                ['username', 'min-length', 3],
                ['username', 'max-length', 20],
                ['username', 'db-exists', ['user', false]],
                ['lastname', 'max-length', 100],
                ['firstname', 'max-length', 100],
                ['email', 'max-length', 255],
                ['email', 'db-exists', ['user', false]],
                ['password', 'min-length', 12],
                ['repassword', 'equals-input', 'password']
            ]);
            if ($checker->check()) {
                $dataset = array_filter([
                    "username" => $_POST['username'],
                    "last_name" => $_POST['lastname'],
                    "first_name" => $_POST['firstname'],
                    "email" => $_POST['email'],
                    "phone" => $_POST['phone'],
                    "address_postal" => $_POST['city_postal'],
                    "address_city" => $_POST['city_name'],
                    "address_street" => $_POST['city_street'],
                    "address_number" => $_POST['city_number'],
                    "password" => password_hash($_POST['password'], PASSWORD_BCRYPT),
                    "created_at" => time()
                ]);
                $userModel = new UserModel();
                $isValidated = $userModel->register($dataset);
                if ($isValidated) {
                    header('Location: /auth/login');
                    exit;
                }
            } else {
                Logger::error('User not created:'.$_POST['username']);
                $messages = $checker->getMessages();
            }
        }

        $page['title'] = '$lang::auth/signin/title';
        $page['description'] = '$lang::auth/signin/description';
        $page['csslist'] = [];
        $page['jspreloadlist'] = [];
        $page['jspostloadlist'] = [];
        new Genfile('auth/signin', $page, ["messages" => $messages]);
    }

    public function TFA() {
        if (AccountChecker::logged()) {
            header('Location: /home/');
            exit;
        }

        $messages = [];
        if (FormChecker::formUploaded()) {
            $code      = preg_replace('/\s+/', '', $_POST['code'] ?? '');
            $userId    = (int) $_SESSION['2fa_pending_user_id'];
            $userModel = new UserModel();
            $twofa     = $userModel->get2FAApp($userId);
            if ($twofa && TOTP::verify($twofa['secret'], $code)) {
                $user = $userModel->findById($userId);
                unset($_SESSION['2fa_pending_user_id']);
                $_SESSION['user_id']    = $userId;
                $_SESSION['username']   = $user['username']   ?? '';
                $_SESSION['first_name'] = $user['first_name'] ?? '';
                $_SESSION['last_name']  = $user['last_name']  ?? '';
                $_SESSION['email']      = $user['email']      ?? '';
                SessionManager::start($userId);
                header('Location: /home/');
                exit;
            }
            $messages['general'][] = 'Code invalide. Veuillez réessayer.';
        }

        $page['title']          = 'Vérification 2FA — TaderLafe';
        $page['csslist']        = ['auth/login'];
        $page['jspreloadlist']  = [];
        $page['jspostloadlist'] = [];
        new Genfile('auth/TFA', $page, ['messages' => $messages]);
    }

    public function logout() {
        if (AccountChecker::logged()) {
            SessionManager::destroy();
            session_destroy();
        }
        header('Location: /');
        exit();
    }

}

?>