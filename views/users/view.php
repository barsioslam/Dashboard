<?php
$avatarColors = ['#4f8ef7','#7c5cfc','#22c97a','#f5a623','#f25f5c','#0ea5e9'];
$color    = $avatarColors[$user['id'] % 6];
$i1       = !empty($user['first_name']) ? strtoupper($user['first_name'][0]) : '';
$i2       = !empty($user['last_name'])  ? strtoupper($user['last_name'][0])  : '';
$initials = $i1 . $i2 ?: strtoupper(substr($user['username'], 0, 2));
$fullName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
$createdAt = !empty($user['created_at']) ? date('d/m/Y à H:i', (int) $user['created_at']) : '—';
$isSelf = (int) $user['id'] === (int) ($_SESSION['user_id'] ?? 0);
?>

<!-- PAGE HEADER -->
<div class="page-header-row">
    <div class="page-header">
        <h1>@<?= htmlspecialchars($user['username']) ?></h1>
        <p class="page-sub">Fiche utilisateur</p>
    </div>
    <div class="page-actions">
        <a href="/users/edit/<?= (int) $user['id'] ?>" class="btn-save">
            <i class="ti ti-edit"></i> Modifier
        </a>
        <a href="/users/list" class="btn-secondary">
            <i class="ti ti-arrow-left"></i> Retour
        </a>
    </div>
</div>

<div class="row-2col-equal" style="margin-bottom:16px;">

    <!-- Left: User info -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">Informations</span>
        </div>

        <div style="display:flex;align-items:center;gap:12px;padding:16px 0 20px;">
            <div class="avatar lg" style="background:<?= htmlspecialchars($color) ?>;">
                <?= htmlspecialchars($initials) ?>
            </div>
            <div>
                <div style="font-size:15px;font-weight:600;color:var(--text);">
                    <?= $fullName ? htmlspecialchars($fullName) : htmlspecialchars($user['username']) ?>
                </div>
                <div style="font-size:12px;color:var(--muted);">
                    <?= htmlspecialchars($user['email']) ?>
                </div>
            </div>
        </div>

        <div class="list">
            <div class="list-item">
                <div class="list-item-body">
                    <div class="list-item-sub">Nom d'utilisateur</div>
                    <div class="list-item-title"><?= htmlspecialchars($user['username']) ?></div>
                </div>
            </div>
            <div class="list-item">
                <div class="list-item-body">
                    <div class="list-item-sub">Adresse e-mail</div>
                    <div class="list-item-title"><?= htmlspecialchars($user['email']) ?></div>
                </div>
            </div>
            <?php if ($fullName): ?>
            <div class="list-item">
                <div class="list-item-body">
                    <div class="list-item-sub">Nom complet</div>
                    <div class="list-item-title"><?= htmlspecialchars($fullName) ?></div>
                </div>
            </div>
            <?php endif; ?>
            <div class="list-item">
                <div class="list-item-body">
                    <div class="list-item-sub">Inscrit le</div>
                    <div class="list-item-title"><?= htmlspecialchars($createdAt) ?></div>
                </div>
            </div>
            <div class="list-item">
                <div class="list-item-body">
                    <div class="list-item-sub">Statut</div>
                    <div class="list-item-title" style="margin-top:4px;">
                        <?php if ($user['is_active']): ?>
                        <span class="badge green">Actif</span>
                        <?php else: ?>
                        <span class="badge red">Inactif</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right: Security & Access -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">Sécurité &amp; Accès</span>
        </div>

        <div class="list">
            <!-- Role -->
            <div class="list-item">
                <div class="list-item-body">
                    <div class="list-item-sub">Rôle</div>
                    <div class="list-item-title" style="margin-top:4px;">
                        <?php if (!empty($user['role_name'])): ?>
                        <span style="
                            display:inline-flex;align-items:center;
                            font-size:11px;font-weight:500;padding:2px 10px;border-radius:20px;
                            background:<?= htmlspecialchars($user['role_color']) ?>22;
                            color:<?= htmlspecialchars($user['role_color']) ?>;
                            border:1px solid <?= htmlspecialchars($user['role_color']) ?>55;
                        "><?= htmlspecialchars($user['role_name']) ?></span>
                        <?php else: ?>
                        <span class="badge muted">Aucun rôle</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <!-- 2FA -->
            <div class="list-item">
                <div class="list-item-body">
                    <div class="list-item-sub">Double authentification (2FA)</div>
                    <div class="list-item-title" style="margin-top:4px;">
                        <?php if ($has2FA): ?>
                        <span class="badge green"><i class="ti ti-shield-check"></i> Activée</span>
                        <?php else: ?>
                        <span class="badge muted">Non configurée</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <!-- Sessions -->
            <div class="list-item">
                <div class="list-item-body">
                    <div class="list-item-sub">Sessions actives</div>
                    <div class="list-item-title"><?= count($sessions) ?> session<?= count($sessions) !== 1 ? 's' : '' ?></div>
                </div>
            </div>
        </div>

        <hr style="border:none;border-top:1px solid var(--border);margin:8px 0;">

        <!-- Actions -->
        <div style="display:flex;flex-direction:column;gap:8px;padding-top:8px;">
            <?php if (!$isSelf): ?>
            <!-- Toggle active -->
            <form method="POST" action="/users/toggleActive/<?= (int) $user['id'] ?>">
                <button type="submit" class="table-btn <?= $user['is_active'] ? 'orange' : 'green' ?>"
                        style="width:100%;justify-content:center;">
                    <i class="ti ti-<?= $user['is_active'] ? 'ban' : 'check' ?>"></i>
                    <?= $user['is_active'] ? 'Désactiver le compte' : 'Activer le compte' ?>
                </button>
            </form>
            <!-- Delete -->
            <form method="POST" action="/users/delete/<?= (int) $user['id'] ?>"
                  onsubmit="return confirm('Supprimer définitivement cet utilisateur ?')">
                <button type="submit" class="table-btn danger" style="width:100%;justify-content:center;">
                    <i class="ti ti-trash"></i> Supprimer l'utilisateur
                </button>
            </form>
            <?php else: ?>
            <p style="font-size:12px;color:var(--muted);text-align:center;padding:8px 0;">
                Actions non disponibles pour votre propre compte.
            </p>
            <?php endif; ?>
        </div>
    </div>

</div>

<!-- Recent activity -->
<div class="card">
    <div class="card-header">
        <span class="card-title">Activité récente</span>
    </div>

    <?php if (empty($recentLogs)): ?>
    <div class="table-empty">Aucune activité enregistrée.</div>
    <?php else: ?>
    <div class="list">
        <?php foreach ($recentLogs as $log):
            $logDate = !empty($log['activity_date']) ? date('d/m/Y H:i', (int) $log['activity_date']) : '—';
        ?>
        <div class="list-item">
            <div class="list-dot blue"></div>
            <div class="list-item-body">
                <div class="list-item-title"><?= htmlspecialchars($log['action']) ?></div>
                <?php if (!empty($log['current'])): ?>
                <div class="list-item-sub"><?= htmlspecialchars($log['current']) ?></div>
                <?php endif; ?>
            </div>
            <div class="list-item-end">
                <span class="list-item-time"><?= htmlspecialchars($logDate) ?></span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
