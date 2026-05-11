<?php
// views/_layouts/dashboard/topbar.php

$username = $_SESSION['username'] ?? 'Admin';
$initials = strtoupper(substr($username, 0, 2));

// Titre de page passé par le controller
$pageLabel = $page['topbar_title'] ?? 'Dashboard';
?>

<header id="topbar">

    <div class="topbar-title"><?= htmlspecialchars($pageLabel) ?></div>

    <!-- Barre de recherche -->
    <div class="topbar-search">
        <i class="ti ti-search" aria-hidden="true"></i>
        <input type="text" placeholder="Rechercher..." aria-label="Rechercher">
    </div>

    <div style="display:flex; align-items:center; gap:8px;">

        <!-- Notifications -->
        <div class="topbar-btn" title="Notifications">
            <i class="ti ti-bell" aria-hidden="true"></i>
            <?php if (!empty($sidebar_counts['notifications'])): ?>
            <div class="notif-dot"></div>
            <?php endif; ?>
        </div>

        <!-- Bugs -->
        <?php if (!empty($sidebar_counts['bugs'])): ?>
        <div class="topbar-btn" title="Bugs signalés">
            <i class="ti ti-bug" aria-hidden="true"></i>
            <div class="notif-dot"></div>
        </div>
        <?php endif; ?>

        <!-- Paramètres -->
        <a href="/home/settings" class="topbar-btn" title="Paramètres">
            <i class="ti ti-settings" aria-hidden="true"></i>
        </a>

        <!-- Avatar utilisateur -->
        <div class="topbar-avatar" title="<?= htmlspecialchars($username) ?>">
            <?= htmlspecialchars($initials) ?>
        </div>

    </div>

</header>