<?php

// HomeController.php

namespace App\Controllers;

use App\Views\Genfile;
use App\Utils\Checker\AccountChecker;
use App\Utils\System\SystemStats;
use App\Utils\Auth\TOTP;
use App\Utils\SessionManager;
use Models\User\UserModel;
use Models\User\UserSessionModel;
use Models\Project\ProjectModel;
use Models\Project\BugModel;
use Models\File\FileModel;
use Models\File\FolderModel;
use Models\Log\ActivityLogModel;
use Models\Chat\MessageModel;
use Models\Notification\NotificationModel;

class HomeController {

    public function __construct() {
        if (!AccountChecker::logged()) {
            header('Location: /auth/login');
            exit;
        }
    }

    public function index(): void {

        $userModel        = new UserModel();
        $projectModel     = new ProjectModel();
        $activityLogModel = new ActivityLogModel();

        $stats = [];
        $stats['users']           = $userModel->countActive();
        $stats['new_users_month'] = $userModel->countNewSince(mktime(0, 0, 0, (int)date('n'), 1, (int)date('Y')));
        $stats['projects']        = $projectModel->count();
        $stats['active_projects'] = $projectModel->countActive();
        $stats['files']           = (new FileModel())->count();
        $stats['folders']         = (new FolderModel())->count();
        $stats['bugs']            = (new BugModel())->count();
        $stats['daily_logins']    = $this->getDailyLogins(7);

        $recent_users    = $userModel->getRecentWithRole(5);
        $recent_projects = $projectModel->getRecent(5);
        $recent_logs     = $activityLogModel->getRecentWithUser(6);

        // --- Compteurs pour sidebar / topbar ---
        $sidebar_counts = $this->getSidebarCounts();

        // --- Ressources système ---
        $sys_stats = SystemStats::get();

        // --- Rendu ---
        $page = [
            'title'          => 'Dashboard — TaderLafe',
            'topbar_title'   => 'Dashboard',
            'csslist'        => ['card', 'chart', 'list', 'stats'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',   // indique à Genfile d'utiliser les layouts dashboard
        ];

        new Genfile('home/index', $page, compact(
            'stats', 'recent_users', 'recent_projects', 'recent_logs', 'sidebar_counts', 'sys_stats'
        ));
    }

    public function settings(): void {

        $messages  = [];
        $userId    = (int)($_SESSION['user_id'] ?? 0);
        $userModel = new UserModel();

        // Charge les données actuelles depuis la BDD
        $currentUser = $userModel->findById($userId) ?? [];

        if (isset($_POST['_section']) && $userId > 0) {

            if ($_POST['_section'] === 'account') {
                $username  = trim($_POST['username']   ?? '');
                $email     = trim($_POST['email']      ?? '');
                $firstName = trim($_POST['first_name'] ?? '');
                $lastName  = trim($_POST['last_name']  ?? '');

                // Validation username
                if (strlen($username) < 3 || strlen($username) > 20) {
                    $messages['username'][] = 'Le nom d\'utilisateur doit contenir entre 3 et 20 caractères.';
                } else {
                    $existing = $userModel->findByUsername($username);
                    if ($existing && (int)$existing['id'] !== $userId) {
                        $messages['username'][] = 'Ce nom d\'utilisateur est déjà utilisé.';
                    }
                }

                // Validation email
                if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $messages['email'][] = 'Adresse e-mail invalide.';
                } else {
                    $existing = $userModel->findByEmail($email);
                    if ($existing && (int)$existing['id'] !== $userId) {
                        $messages['email'][] = 'Cette adresse e-mail est déjà utilisée.';
                    }
                }

                // Préserve les valeurs saisies (erreur ou succès)
                $currentUser = array_merge($currentUser, [
                    'username'   => $username,
                    'email'      => $email,
                    'first_name' => $firstName,
                    'last_name'  => $lastName,
                ]);

                if (empty($messages)) {
                    $userModel->update($userId, [
                        'username'   => $username,
                        'email'      => $email,
                        'first_name' => $firstName,
                        'last_name'  => $lastName,
                    ]);
                    $_SESSION['username']   = $username;
                    $_SESSION['email']      = $email;
                    $_SESSION['first_name'] = $firstName;
                    $_SESSION['last_name']  = $lastName;
                    $messages['success'][]  = 'Informations mises à jour avec succès.';
                }

            } elseif ($_POST['_section'] === '2fa_init') {
                if (!$userModel->has2FAApp($userId)) {
                    $_SESSION['2fa_setup_secret'] = TOTP::generateSecret();
                }
                header('Location: /home/settings?tab=security');
                exit;

            } elseif ($_POST['_section'] === '2fa_cancel') {
                unset($_SESSION['2fa_setup_secret']);
                header('Location: /home/settings?tab=security');
                exit;

            } elseif ($_POST['_section'] === '2fa_confirm') {
                
                $secret = $_SESSION['2fa_setup_secret'] ?? null;
                $code   = preg_replace('/\s+/', '', $_POST['code'] ?? '');
                if ($secret && TOTP::verify($secret, $code)) {
                    $userModel->enable2FA($userId, $secret);
                    unset($_SESSION['2fa_setup_secret']);
                    $messages['2fa_success'] = true;
                } else {
                    $messages['2fa_error'] = true;
                }

            } elseif ($_POST['_section'] === '2fa_disable') {
                $userModel->disable2FA($userId);
                unset($_SESSION['2fa_setup_secret']);
                $messages['2fa_disabled'] = true;

            } elseif ($_POST['_section'] === 'session_revoke') {
                $token        = $_POST['token'] ?? '';
                $sessionModel = new UserSessionModel();
                if ($token && $token !== ($_SESSION['session_token'] ?? '')) {
                    $sessionModel->revokeForUser($token, $userId);
                    $messages['session_revoked'] = true;
                }
                header('Location: /home/settings?tab=security');
                exit;

            } elseif ($_POST['_section'] === 'session_revoke_all') {
                $currentToken = $_SESSION['session_token'] ?? '';
                (new UserSessionModel())->revokeAllExcept($currentToken, $userId);
                $messages['sessions_revoked'] = true;
                header('Location: /home/settings?tab=security');
                exit;
            }
        }

        $tfaEnabled     = $userModel->has2FAApp($userId);
        $tfaSetupSecret = !$tfaEnabled ? ($_SESSION['2fa_setup_secret'] ?? null) : null;
        $tfaSetupUri    = $tfaSetupSecret
            ? TOTP::getOtpAuthUri($tfaSetupSecret, $currentUser['username'] ?? 'user')
            : null;

        $sessionModel   = new UserSessionModel();
        $activeSessions = $sessionModel->getByUser($userId);
        $currentToken   = $_SESSION['session_token'] ?? '';

        $page = [
            'title'          => 'Paramètres — TaderLafe',
            'topbar_title'   => 'Paramètres',
            'csslist'        => ['card', 'home/settings'],
            'jspreloadlist'  => [],
            'jspostloadlist' => [],
            'layout'         => 'dashboard',
        ];

        new Genfile('home/settings', $page, compact(
            'messages', 'currentUser',
            'tfaEnabled', 'tfaSetupSecret', 'tfaSetupUri',
            'activeSessions', 'currentToken'
        ));
    }

    // -------------------------------------------------------
    //  Helpers privés
    // -------------------------------------------------------

    /**
     * Retourne le nombre de connexions (action='login') par jour sur N jours.
     * Renvoie un tableau de N entiers (du plus ancien au plus récent).
     */
    private function getDailyLogins(int $days = 7): array {
        $counts = array_fill(0, $days, 0);
        $rows   = (new ActivityLogModel())->getLoginsSince(time() - ($days * 86400));

        foreach ($rows as $row) {
            $dayIndex = (int)floor((time() - (int)$row['activity_date']) / 86400);
            $slot     = $days - 1 - $dayIndex;
            if ($slot >= 0 && $slot < $days) {
                $counts[$slot]++;
            }
        }

        // Si tout est à 0 (BDD vide), renvoie des données de démo
        if (array_sum($counts) === 0) {
            return [1, 2, 4, 8, 16, 32, 64];
        }

        return $counts;
    }

    /**
     * Retourne les compteurs affichés dans la sidebar et la topbar.
     */
    private function getSidebarCounts(): array {
        return [
            'messages'      => (new MessageModel())->countSince(time() - 86400),
            'notifications' => (new NotificationModel())->countActive(),
            'bugs'          => (new BugModel())->count(),
            'users'         => (new UserModel())->countActive(),
        ];
    }
}