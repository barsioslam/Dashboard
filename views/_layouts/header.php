<?php
use App\Utils\Text\Text;
?>

<!DOCTYPE html>
<html lang="<?= htmlspecialchars($_SESSION['lang'] ?? 'fr'); ?>">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- TITLE -->
        <?php
        $title = Text::write($page['title'] ?? '');
        if (empty($title)) {
            $title = "[MISSING TITLE]";
        }
        $desc = Text::write($page['description'] ?? '');
        if (empty($desc)) {
            $desc = "[MISSING DESCRIPTION]";
        }
        $ogUrl = ($_SERVER['HTTPS'] ?? 'off') === 'on' ? 'https' : 'http';
        $ogUrl .= '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        ?>
        <title><?= $title; ?></title>
        <meta name="description" content="<?= $desc; ?>">

        <!-- Auteur -->
        <meta name="author" content="TaderLafe Team">

        <!-- Open Graph (pour les aperçus sur Facebook, Discord, etc.) -->
        <meta property="og:title" content="<?= $title; ?>">
        <meta property="og:description" content="<?= $desc; ?>">
        <meta property="og:type" content="website">
        <meta property="og:url" content="<?= htmlspecialchars($ogUrl); ?>">
        <meta property="og:image" content="https://taderlafe.com/favicon.ico">

        <!-- Twitter Card (pour les aperçus sur Twitter/X) -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="<?= $title; ?>">
        <meta name="twitter:description" content="<?= $desc; ?>">
        <meta name="twitter:image" content="https://taderlafe.com/favicon.ico">

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="/favicon.png">

        <!-- Tabler Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">

        <!-- CSS LOADING -->
        <link rel="stylesheet" href="/assets/css/general.css">
        <link rel="stylesheet" href="/assets/css/variables.css">
        <link rel="stylesheet" href="/assets/css/layout.css">
        <link rel="stylesheet" href="/assets/css/topbar.css">
        <link rel="stylesheet" href="/assets/css/sidebar.css">
        <?php
        if (isset($page['csslist']) && is_array($page['csslist'])) {
            foreach ($page['csslist'] as $cssFile) {
                echo '<link rel="stylesheet" href="/assets/css/' . htmlspecialchars($cssFile) . '.css">' . "\n";
            }
        }
        ?>

        <!-- JS PRELOAD FILES -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js" defer></script>
        <?php
        if (isset($page['jspreloadlist']) && is_array($page['jspreloadlist'])) {
            foreach ($page['jspreloadlist'] as $jsFile) {
                echo '<script src="/assets/js/' . htmlspecialchars($jsFile) . '.js" defer></script>' . "\n";
            }
        }
        ?>
    </head>
    <body>
        <?php if (!empty($page['layout'])): ?>
            <?php require(LAYOUT_PATH . 'home/sidebar.php'); ?>
            <div id="dashboard-layout">
                <div id="dashboard-main">
                    <?php require(LAYOUT_PATH . 'home/topbar.php'); ?>
                    <div id="dashboard-content">
        <?php elseif (!empty($page['navbar'])): ?>
            <?php require(LAYOUT_PATH . $page['navbar'] . '/navbar.php'); ?>
        <?php endif; ?>
