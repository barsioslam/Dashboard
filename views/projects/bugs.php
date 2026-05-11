<?php
use Models\Project\BugStatusModel;

$totalBugs = array_sum($bugCounts);

// $bugStatus = null (tous) ou int (0/1/2)
// $bugStatusParam = '' ou '0'/'1'/'2' (depuis $_GET)
$baseUrl = '/projects/bugs/' . (int) $project['id'];
?>

<!-- PAGE HEADER -->
<div class="page-header-row">
    <div class="page-header">
        <h1>Bugs — <?= htmlspecialchars($project['name']) ?></h1>
        <p class="page-sub">
            <?= $totalBugs ?> bug<?= $totalBugs !== 1 ? 's' : '' ?> au total
        </p>
    </div>
    <div class="page-actions">
        <a href="/projects/view/<?= (int) $project['id'] ?>" class="btn-secondary">
            <i class="ti ti-arrow-left"></i> Fiche projet
        </a>
    </div>
</div>

<!-- Signaler un bug -->
<?php if (!$project['is_read_only']): ?>
<div class="card" style="margin-bottom:16px;">
    <div class="card-header">
        <span class="card-title">Signaler un bug</span>
    </div>
    <form method="POST" action="/projects/addBug/<?= (int) $project['id'] ?>">
        <div class="field">
            <label for="bug_title">Titre <span style="color:var(--danger)">*</span></label>
            <input type="text" id="bug_title" name="title" required
                   placeholder="Ex : Erreur 500 sur la page de connexion">
        </div>
        <div class="field">
            <label for="bug_description">Description</label>
            <textarea id="bug_description" name="description" rows="3"
                      placeholder="Étapes pour reproduire, comportement attendu…"></textarea>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn-save">
                <i class="ti ti-bug"></i> Signaler le bug
            </button>
        </div>
    </form>
</div>
<?php endif; ?>

<!-- Liste des bugs -->
<div class="card">
    <div class="card-header">
        <div class="filter-tabs">
            <a href="<?= $baseUrl ?>" class="filter-tab <?= $bugStatus === null ? 'active' : '' ?>">
                Tous
                <span style="display:inline-flex;align-items:center;justify-content:center;
                    background:var(--surface-2,#e5e7eb);border-radius:10px;
                    font-size:10px;min-width:16px;height:16px;padding:0 4px;margin-left:4px;">
                    <?= $totalBugs ?>
                </span>
            </a>
            <a href="<?= $baseUrl ?>?status=<?= BugStatusModel::OPEN ?>"
               class="filter-tab <?= $bugStatus === BugStatusModel::OPEN ? 'active' : '' ?>">
                Ouverts
                <span style="display:inline-flex;align-items:center;justify-content:center;
                    background:#fee2e2;color:#dc2626;border-radius:10px;
                    font-size:10px;min-width:16px;height:16px;padding:0 4px;margin-left:4px;">
                    <?= $bugCounts[BugStatusModel::OPEN] ?>
                </span>
            </a>
            <a href="<?= $baseUrl ?>?status=<?= BugStatusModel::IN_PROGRESS ?>"
               class="filter-tab <?= $bugStatus === BugStatusModel::IN_PROGRESS ? 'active' : '' ?>">
                En cours
                <span style="display:inline-flex;align-items:center;justify-content:center;
                    background:#fef3c7;color:#d97706;border-radius:10px;
                    font-size:10px;min-width:16px;height:16px;padding:0 4px;margin-left:4px;">
                    <?= $bugCounts[BugStatusModel::IN_PROGRESS] ?>
                </span>
            </a>
            <a href="<?= $baseUrl ?>?status=<?= BugStatusModel::CLOSED ?>"
               class="filter-tab <?= $bugStatus === BugStatusModel::CLOSED ? 'active' : '' ?>">
                Fermés
                <span style="display:inline-flex;align-items:center;justify-content:center;
                    background:#d1fae5;color:#059669;border-radius:10px;
                    font-size:10px;min-width:16px;height:16px;padding:0 4px;margin-left:4px;">
                    <?= $bugCounts[BugStatusModel::CLOSED] ?>
                </span>
            </a>
        </div>
    </div>

    <div class="table-wrap">
        <div class="table">
            <div class="row header">
                <div class="cell shrink">#</div>
                <div class="cell">Titre</div>
                <div class="cell">Signalé par</div>
                <div class="cell">Date</div>
                <div class="cell shrink">Statut</div>
                <div class="cell actions">Actions</div>
            </div>

            <?php if (empty($bugs)): ?>
            <div class="table-empty">Aucun bug trouvé.</div>
            <?php else: foreach ($bugs as $bug):
                $status  = (int) $bug['status'];
                $cls     = BugStatusModel::cssClass($status);
                $label   = BugStatusModel::label($status);
                $bugDate = !empty($bug['created_at']) ? date('d/m/Y', (int) $bug['created_at']) : '—';
            ?>
            <div class="row">
                <div class="cell shrink muted"><?= (int) $bug['id'] ?></div>
                <div class="cell">
                    <div style="font-weight:500;font-size:13px;"><?= htmlspecialchars($bug['title']) ?></div>
                    <?php if (!empty($bug['content'])): ?>
                    <div style="font-size:11px;color:var(--muted);">
                        <?= htmlspecialchars(mb_strimwidth($bug['content'], 0, 80, '…')) ?>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="cell muted"><?= htmlspecialchars($bug['reporter'] ?? '—') ?></div>
                <div class="cell muted"><?= $bugDate ?></div>
                <div class="cell shrink">
                    <span class="badge <?= $cls ?>"><?= $label ?></span>
                </div>
                <div class="cell actions">
                    <?php if ($status !== BugStatusModel::IN_PROGRESS): ?>
                    <form method="POST" action="/projects/updateBugStatus/<?= (int) $bug['id'] ?>" style="display:inline;">
                        <input type="hidden" name="status" value="<?= BugStatusModel::IN_PROGRESS ?>">
                        <button type="submit" class="table-btn orange" title="Marquer en cours">
                            <i class="ti ti-progress"></i>
                        </button>
                    </form>
                    <?php endif; ?>
                    <?php if ($status !== BugStatusModel::CLOSED): ?>
                    <form method="POST" action="/projects/updateBugStatus/<?= (int) $bug['id'] ?>" style="display:inline;">
                        <input type="hidden" name="status" value="<?= BugStatusModel::CLOSED ?>">
                        <button type="submit" class="table-btn green" title="Marquer fermé">
                            <i class="ti ti-check"></i>
                        </button>
                    </form>
                    <?php endif; ?>
                    <?php if ($status !== BugStatusModel::OPEN): ?>
                    <form method="POST" action="/projects/updateBugStatus/<?= (int) $bug['id'] ?>" style="display:inline;">
                        <input type="hidden" name="status" value="<?= BugStatusModel::OPEN ?>">
                        <button type="submit" class="table-btn" title="Rouvrir">
                            <i class="ti ti-refresh"></i>
                        </button>
                    </form>
                    <?php endif; ?>
                    <form method="POST" action="/projects/deleteBug/<?= (int) $bug['id'] ?>" style="display:inline;"
                          onsubmit="return confirm('Supprimer ce bug ?')">
                        <button type="submit" class="table-btn danger">
                            <i class="ti ti-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
</div>
