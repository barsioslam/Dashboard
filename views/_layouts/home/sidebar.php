<?php
// views/_layouts/dashboard/sidebar.php
use App\Utils\Text\Text;
use App\Utils\Checker\AccountChecker;

// Récupère la section active depuis l'URL
$currentSegment = explode('/', $_GET['url'] ?? 'dashboard/index');
$activeSection  = $currentSegment[0] ?? 'dashboard';

// Initiales de l'utilisateur connecté pour l'avatar
$username  = $_SESSION['username']  ?? 'Admin';
$userRole  = $_SESSION['role_name'] ?? 'Admin';
$initials  = strtoupper(substr($username, 0, 2));
?>

<aside id="sidebar">

    <!-- Logo -->
    <div class="sidebar-logo">
        <div class="sidebar-logo-icon">TL</div>
        <div class="sidebar-logo-text">
            <div class="name">TaderLafe</div>
            <div class="sub">Admin Panel</div>
        </div>
    </div>

    <!-- Nav principale -->
    <nav class="nav-section">
        <div class="nav-section-label">Principal</div>

        <a href="/home/index" class="nav-item <?= $activeSection === 'dashboard' ? 'active' : '' ?>">
            <i class="ti ti-layout-dashboard" aria-hidden="true"></i>
            Dashboard
        </a>

        <a href="/users/list" class="nav-item <?= $activeSection === 'users' ? 'active' : '' ?>">
            <i class="ti ti-users" aria-hidden="true"></i>
            Utilisateurs
            <?php if (!empty($sidebar_counts['users'])): ?>
            <span class="nav-badge"><?= (int)$sidebar_counts['users'] ?></span>
            <?php endif; ?>
        </a>

        <a href="/groups/list" class="nav-item <?= $activeSection === 'groups' ? 'active' : '' ?>">
            <i class="ti ti-users-group" aria-hidden="true"></i>
            Groupes
        </a>

        <a href="/projects/list" class="nav-item <?= $activeSection === 'projects' ? 'active' : '' ?>">
            <i class="ti ti-folders" aria-hidden="true"></i>
            Projets
        </a>

        <a href="/roles/list" class="nav-item <?= $activeSection === 'roles' ? 'active' : '' ?>">
            <i class="ti ti-shield-check" aria-hidden="true"></i>
            Rôles &amp; Permissions
        </a>
    </nav>

    <!-- Outils -->
    <nav class="nav-section">
        <div class="nav-section-label">Outils</div>

        <a href="/messages/list" class="nav-item <?= $activeSection === 'messages' ? 'active' : '' ?>">
            <i class="ti ti-message-2" aria-hidden="true"></i>
            Messages
            <?php if (!empty($sidebar_counts['messages'])): ?>
            <span class="nav-badge warn"><?= (int)$sidebar_counts['messages'] ?></span>
            <?php endif; ?>
        </a>

        <a href="/files/list" class="nav-item <?= $activeSection === 'files' ? 'active' : '' ?>">
            <i class="ti ti-folder-open" aria-hidden="true"></i>
            Fichiers
        </a>

        <a href="/todos/list" class="nav-item <?= $activeSection === 'todos' ? 'active' : '' ?>">
            <i class="ti ti-checklist" aria-hidden="true"></i>
            Todo Lists
        </a>

        <a href="/notifications/list" class="nav-item <?= $activeSection === 'notifications' ? 'active' : '' ?>">
            <i class="ti ti-bell" aria-hidden="true"></i>
            Notifications
            <?php if (!empty($sidebar_counts['notifications'])): ?>
            <span class="nav-badge danger"><?= (int)$sidebar_counts['notifications'] ?></span>
            <?php endif; ?>
        </a>
    </nav>

    <!-- Système -->
    <nav class="nav-section">
        <div class="nav-section-label">Système</div>

        <a href="/logs/list" class="nav-item <?= $activeSection === 'logs' ? 'active' : '' ?>">
            <i class="ti ti-activity" aria-hidden="true"></i>
            Logs d'activité
        </a>

        <a href="/home/settings" class="nav-item <?= $activeSection === 'settings' ? 'active' : '' ?>">
            <i class="ti ti-settings" aria-hidden="true"></i>
            Paramètres
        </a>
    </nav>

    <!-- Footer utilisateur -->
    <div class="sidebar-footer">
        <div class="avatar" style="background: linear-gradient(135deg, var(--accent2), var(--accent))">
            <?= htmlspecialchars($initials) ?>
        </div>
        <div style="flex:1; min-width:0;">
            <div class="user-name"><?= htmlspecialchars($username) ?></div>
            <div class="user-role"><?= htmlspecialchars($userRole) ?></div>
        </div>
        <a href="/auth/logout" class="logout-btn" title="Déconnexion">
            <i class="ti ti-logout" aria-hidden="true"></i>
        </a>
    </div>

</aside>