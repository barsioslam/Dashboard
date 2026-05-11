<?php
$qsBase = $search ? '?q=' . urlencode($search) . '&' : '?';
?>

<!-- PAGE HEADER -->
<div class="page-header-row">
    <div class="page-header">
        <h1>Groupes</h1>
        <p class="page-sub">
            <?= (int) $total ?> groupe<?= $total !== 1 ? 's' : '' ?> au total
        </p>
    </div>
    <div class="page-actions">
        <a href="/groups/create" class="btn-save">
            <i class="ti ti-users-group"></i> Créer un groupe
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="filter-tabs">
            <span class="filter-tab active">Tous</span>
        </div>
        <form method="GET" action="/groups/list" style="display:flex;align-items:center;gap:6px;">
            <input type="search" name="q" class="search-input" placeholder="Rechercher…"
                value="<?= htmlspecialchars($search) ?>">
        </form>
    </div>

    <div class="table-wrap">
        <div class="table">
            <div class="row header">
                <div class="cell shrink">#</div>
                <div class="cell">Nom</div>
                <div class="cell">Description</div>
                <div class="cell shrink">Membres</div>
                <div class="cell">Créé le</div>
                <div class="cell actions">Actions</div>
            </div>

            <?php if (empty($groups)): ?>
            <div class="table-empty">Aucun groupe trouvé.</div>
            <?php else: foreach ($groups as $g):
                $date = !empty($g['created_at']) ? date('d/m/Y', (int) $g['created_at']) : '—';
            ?>
            <div class="row">
                <div class="cell shrink muted"><?= (int) $g['id'] ?></div>
                <div class="cell">
                    <div style="font-weight:500;font-size:13px;"><?= htmlspecialchars($g['name']) ?></div>
                </div>
                <div class="cell muted" style="font-size:12px;">
                    <?= $g['description'] ? htmlspecialchars(mb_strimwidth($g['description'], 0, 60, '…')) : '—' ?>
                </div>
                <div class="cell shrink">
                    <span class="badge"><?= (int) $g['member_count'] ?></span>
                </div>
                <div class="cell muted"><?= htmlspecialchars($date) ?></div>
                <div class="cell actions">
                    <a href="/groups/view/<?= (int) $g['id'] ?>" class="table-btn">
                        <i class="ti ti-eye"></i>
                    </a>
                    <a href="/groups/edit/<?= (int) $g['id'] ?>" class="table-btn blue">
                        <i class="ti ti-edit"></i>
                    </a>
                    <form method="POST" action="/groups/delete/<?= (int) $g['id'] ?>" style="display:inline;"
                        onsubmit="return confirm('Supprimer ce groupe ?')">
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
        <a href="/groups/list<?= $qsBase ?>page=<?= $page - 1 ?>" class="table-btn">
            <i class="ti ti-chevron-left"></i>
        </a>
        <?php endif; ?>

        <?php for ($p = 1; $p <= $pages; $p++):
            if ($p === 1 || $p === $pages || abs($p - $page) <= 2):
        ?>
        <a href="/groups/list<?= $qsBase ?>page=<?= $p ?>" class="table-btn <?= $p === $page ? 'blue' : '' ?>">
            <?= $p ?>
        </a>
        <?php elseif (abs($p - $page) === 3): ?>
        <span style="color:var(--muted);padding:0 4px;">…</span>
        <?php endif; ?>
        <?php endfor; ?>

        <?php if ($page < $pages): ?>
        <a href="/groups/list<?= $qsBase ?>page=<?= $page + 1 ?>" class="table-btn">
            <i class="ti ti-chevron-right"></i>
        </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
