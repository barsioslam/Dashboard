<?php
$avatarColors = ['#4f8ef7','#7c5cfc','#22c97a','#f5a623','#f25f5c','#0ea5e9'];

// Build base query string without page
$qsBase = http_build_query(array_filter([
    'q'      => $search,
    'status' => $status,
], fn($v) => $v !== ''));
$qsBase = $qsBase ? '?' . $qsBase . '&' : '?';
?>

<!-- PAGE HEADER -->
<div class="page-header-row">
    <div class="page-header">
        <h1>Utilisateurs</h1>
        <p class="page-sub">
            <?= (int) $total ?> utilisateur<?= $total !== 1 ? 's' : '' ?> au total
        </p>
    </div>
    <div class="page-actions">
        <a href="/users/create" class="btn-save">
            <i class="ti ti-user-plus"></i> Créer un utilisateur
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <!-- Filter tabs -->
        <div class="filter-tabs">
            <a href="/users/list<?= $search ? '?q=' . urlencode($search) : '' ?>"
               class="filter-tab <?= $status === '' ? 'active' : '' ?>">Tous</a>
            <a href="/users/list?status=active<?= $search ? '&q=' . urlencode($search) : '' ?>"
               class="filter-tab <?= $status === 'active' ? 'active' : '' ?>">Actifs</a>
            <a href="/users/list?status=inactive<?= $search ? '&q=' . urlencode($search) : '' ?>"
               class="filter-tab <?= $status === 'inactive' ? 'active' : '' ?>">Inactifs</a>
        </div>
        <!-- Search -->
        <form method="GET" action="/users/list" style="display:flex;align-items:center;gap:6px;">
            <?php if ($status): ?>
            <input type="hidden" name="status" value="<?= htmlspecialchars($status) ?>">
            <?php endif; ?>
            <input type="search" name="q" class="search-input"
                   placeholder="Rechercher…"
                   value="<?= htmlspecialchars($search) ?>">
        </form>
    </div>

    <div class="table-wrap">
        <div class="table">
            <!-- Header -->
            <div class="row header">
                <div class="cell shrink">#</div>
                <div class="cell">Utilisateur</div>
                <div class="cell">Email</div>
                <div class="cell">Rôle</div>
                <div class="cell">Inscrit le</div>
                <div class="cell shrink">Statut</div>
                <div class="cell actions">Actions</div>
            </div>

            <?php if (empty($users)): ?>
            <div class="table-empty">Aucun utilisateur trouvé.</div>
            <?php else: foreach ($users as $u):
                $color    = $avatarColors[$u['id'] % 6];
                $i1       = !empty($u['first_name']) ? strtoupper($u['first_name'][0]) : '';
                $i2       = !empty($u['last_name'])  ? strtoupper($u['last_name'][0])  : '';
                $initials = $i1 . $i2 ?: strtoupper(substr($u['username'], 0, 2));
                $date     = $u['created_at'] ? date('d/m/Y', (int) $u['created_at']) : '—';
            ?>
            <div class="row">
                <div class="cell shrink muted"><?= (int) $u['id'] ?></div>
                <div class="cell">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <div class="avatar" style="background:<?= htmlspecialchars($color) ?>;">
                            <?= htmlspecialchars($initials) ?>
                        </div>
                        <div>
                            <div style="font-weight:500;font-size:13px;">
                                <?= htmlspecialchars($u['username']) ?>
                            </div>
                            <?php if (!empty($u['first_name']) || !empty($u['last_name'])): ?>
                            <div style="font-size:11px;color:var(--muted);">
                                <?= htmlspecialchars(trim($u['first_name'] . ' ' . $u['last_name'])) ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="cell muted"><?= htmlspecialchars($u['email']) ?></div>
                <div class="cell">
                    <?php if (!empty($u['role_name'])): ?>
                    <span style="
                        display:inline-flex;align-items:center;
                        font-size:10px;font-weight:500;padding:2px 8px;border-radius:20px;
                        background:<?= htmlspecialchars($u['role_color']) ?>22;
                        color:<?= htmlspecialchars($u['role_color']) ?>;
                        border:1px solid <?= htmlspecialchars($u['role_color']) ?>55;
                    "><?= htmlspecialchars($u['role_name']) ?></span>
                    <?php else: ?>
                    <span class="badge muted">—</span>
                    <?php endif; ?>
                </div>
                <div class="cell muted"><?= htmlspecialchars($date) ?></div>
                <div class="cell shrink">
                    <?php if ($u['is_active']): ?>
                    <span class="badge green">Actif</span>
                    <?php else: ?>
                    <span class="badge red">Inactif</span>
                    <?php endif; ?>
                </div>
                <div class="cell actions">
                    <a href="/users/view/<?= (int) $u['id'] ?>" class="table-btn">
                        <i class="ti ti-eye"></i>
                    </a>
                    <a href="/users/edit/<?= (int) $u['id'] ?>" class="table-btn blue">
                        <i class="ti ti-edit"></i>
                    </a>
                    <!-- Toggle active -->
                    <form method="POST" action="/users/toggleActive/<?= (int) $u['id'] ?>" style="display:inline;">
                        <button type="submit" class="table-btn <?= $u['is_active'] ? 'orange' : 'green' ?>"
                                title="<?= $u['is_active'] ? 'Désactiver' : 'Activer' ?>">
                            <i class="ti ti-<?= $u['is_active'] ? 'ban' : 'check' ?>"></i>
                        </button>
                    </form>
                    <!-- Delete -->
                    <form method="POST" action="/users/delete/<?= (int) $u['id'] ?>" style="display:inline;"
                          onsubmit="return confirm('Supprimer cet utilisateur ?')">
                        <button type="submit" class="table-btn danger">
                            <i class="ti ti-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; endif; ?>
        </div>
    </div>

    <?php if ($pages > 1): ?>
    <div class="card-footer" style="display:flex;align-items:center;gap:4px;justify-content:center;padding:12px;">
        <?php if ($page > 1): ?>
        <a href="/users/list<?= $qsBase ?>page=<?= $page - 1 ?>" class="table-btn">
            <i class="ti ti-chevron-left"></i>
        </a>
        <?php endif; ?>

        <?php for ($p = 1; $p <= $pages; $p++):
            if ($p === 1 || $p === $pages || abs($p - $page) <= 2):
        ?>
        <a href="/users/list<?= $qsBase ?>page=<?= $p ?>"
           class="table-btn <?= $p === $page ? 'blue' : '' ?>">
            <?= $p ?>
        </a>
        <?php elseif (abs($p - $page) === 3): ?>
        <span style="color:var(--muted);padding:0 4px;">…</span>
        <?php endif; ?>
        <?php endfor; ?>

        <?php if ($page < $pages): ?>
        <a href="/users/list<?= $qsBase ?>page=<?= $page + 1 ?>" class="table-btn">
            <i class="ti ti-chevron-right"></i>
        </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
