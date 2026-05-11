<?php
$qsBase = http_build_query(array_filter(['q' => $search, 'status' => $status], fn($v) => $v !== ''));
$qsBase = $qsBase ? '?' . $qsBase . '&' : '?';
?>

<!-- PAGE HEADER -->
<div class="page-header-row">
    <div class="page-header">
        <h1>Projets</h1>
        <p class="page-sub">
            <?= (int) $total ?> projet<?= $total !== 1 ? 's' : '' ?> au total
        </p>
    </div>
    <div class="page-actions">
        <a href="/projects/create" class="btn-save">
            <i class="ti ti-folder-plus"></i> Nouveau projet
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="filter-tabs">
            <a href="/projects/list<?= $search ? '?q=' . urlencode($search) : '' ?>"
               class="filter-tab <?= $status === '' ? 'active' : '' ?>">Tous</a>
            <a href="/projects/list?status=active<?= $search ? '&q=' . urlencode($search) : '' ?>"
               class="filter-tab <?= $status === 'active' ? 'active' : '' ?>">Actifs</a>
            <a href="/projects/list?status=archived<?= $search ? '&q=' . urlencode($search) : '' ?>"
               class="filter-tab <?= $status === 'archived' ? 'active' : '' ?>">Archivés</a>
        </div>
        <form method="GET" action="/projects/list" style="display:flex;align-items:center;gap:6px;">
            <?php if ($status): ?>
            <input type="hidden" name="status" value="<?= htmlspecialchars($status) ?>">
            <?php endif; ?>
            <input type="search" name="q" class="search-input" placeholder="Rechercher…"
                value="<?= htmlspecialchars($search) ?>">
        </form>
    </div>

    <div class="table-wrap">
        <div class="table">
            <div class="row header">
                <div class="cell shrink">#</div>
                <div class="cell">Projet</div>
                <div class="cell shrink" style="text-align:center;">Membres</div>
                <div class="cell shrink" style="text-align:center;">Bugs</div>
                <div class="cell">Créé le</div>
                <div class="cell shrink">Statut</div>
                <div class="cell actions">Actions</div>
            </div>

            <?php if (empty($projects)): ?>
            <div class="table-empty">Aucun projet trouvé.</div>
            <?php else: foreach ($projects as $p):
                $date = !empty($p['created_at']) ? date('d/m/Y', (int) $p['created_at']) : '—';
            ?>
            <div class="row">
                <div class="cell shrink muted"><?= (int) $p['id'] ?></div>
                <div class="cell">
                    <div style="font-weight:500;font-size:13px;"><?= htmlspecialchars($p['name']) ?></div>
                    <?php if (!empty($p['description'])): ?>
                    <div style="font-size:11px;color:var(--muted);">
                        <?= htmlspecialchars(mb_strimwidth($p['description'], 0, 60, '…')) ?>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="cell shrink" style="text-align:center;">
                    <span class="badge"><?= (int) $p['member_count'] ?></span>
                </div>
                <div class="cell shrink" style="text-align:center;">
                    <span class="badge <?= (int) $p['bug_count'] > 0 ? 'red' : '' ?>"><?= (int) $p['bug_count'] ?></span>
                </div>
                <div class="cell muted"><?= htmlspecialchars($date) ?></div>
                <div class="cell shrink">
                    <?php if ($p['is_read_only']): ?>
                    <span class="badge muted">Archivé</span>
                    <?php else: ?>
                    <span class="badge green">Actif</span>
                    <?php endif; ?>
                </div>
                <div class="cell actions">
                    <a href="/projects/view/<?= (int) $p['id'] ?>" class="table-btn">
                        <i class="ti ti-eye"></i>
                    </a>
                    <a href="/projects/bugs/<?= (int) $p['id'] ?>" class="table-btn">
                        <i class="ti ti-bug"></i>
                    </a>
                    <a href="/projects/edit/<?= (int) $p['id'] ?>" class="table-btn blue">
                        <i class="ti ti-edit"></i>
                    </a>
                    <form method="POST" action="/projects/delete/<?= (int) $p['id'] ?>" style="display:inline;"
                        onsubmit="return confirm('Supprimer ce projet ?')">
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
        <?php if ($projectpage > 1): ?>
        <a href="/projects/list<?= $qsBase ?>page=<?= $projectpage - 1 ?>" class="table-btn">
            <i class="ti ti-chevron-left"></i>
        </a>
        <?php endif; ?>
        <?php for ($pg = 1; $pg <= $pages; $pg++):
            if ($pg === 1 || $pg === $pages || abs($pg - $projectpage) <= 2):
        ?>
        <a href="/projects/list<?= $qsBase ?>page=<?= $pg ?>"
           class="table-btn <?= $pg === $projectpage ? 'blue' : '' ?>"><?= $pg ?></a>
        <?php elseif (abs($pg - $projectpage) === 3): ?>
        <span style="color:var(--muted);padding:0 4px;">…</span>
        <?php endif; ?>
        <?php endfor; ?>
        <?php if ($projectpage < $pages): ?>
        <a href="/projects/list<?= $qsBase ?>page=<?= $projectpage + 1 ?>" class="table-btn">
            <i class="ti ti-chevron-right"></i>
        </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
