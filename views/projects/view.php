<?php
use Models\Project\BugStatusModel;

$avatarColors = ['#4f8ef7','#7c5cfc','#22c97a','#f5a623','#f25f5c','#0ea5e9'];
$createdAt    = !empty($project['created_at']) ? date('d/m/Y à H:i', (int) $project['created_at']) : '—';
$totalBugs    = array_sum($bugCounts);
?>

<!-- PAGE HEADER -->
<div class="page-header-row">
    <div class="page-header">
        <h1><?= htmlspecialchars($project['name']) ?></h1>
        <p class="page-sub">Fiche projet</p>
    </div>
    <div class="page-actions">
        <a href="/files/project/<?= (int) $project['id'] ?>" class="btn-secondary">
            <i class="ti ti-folders"></i> Fichiers
        </a>
        <a href="/projects/bugs/<?= (int) $project['id'] ?>" class="btn-secondary">
            <i class="ti ti-bug"></i> Bugs
            <?php if ($bugCounts[BugStatusModel::OPEN] > 0): ?>
            <span style="
                display:inline-flex;align-items:center;justify-content:center;
                background:var(--danger);color:#fff;border-radius:10px;
                font-size:10px;font-weight:700;min-width:16px;height:16px;padding:0 4px;
                margin-left:4px;
            "><?= $bugCounts[BugStatusModel::OPEN] ?></span>
            <?php endif; ?>
        </a>
        <a href="/projects/edit/<?= (int) $project['id'] ?>" class="btn-save">
            <i class="ti ti-edit"></i> Modifier
        </a>
        <a href="/projects/list" class="btn-secondary">
            <i class="ti ti-arrow-left"></i> Retour
        </a>
    </div>
</div>

<div class="row-2col-equal" style="margin-bottom:16px;">

    <!-- Informations -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">Informations</span>
        </div>
        <div class="list">
            <div class="list-item">
                <div class="list-item-body">
                    <div class="list-item-sub">Nom du projet</div>
                    <div class="list-item-title"><?= htmlspecialchars($project['name']) ?></div>
                </div>
            </div>
            <?php if (!empty($project['description'])): ?>
            <div class="list-item">
                <div class="list-item-body">
                    <div class="list-item-sub">Description</div>
                    <div class="list-item-title" style="white-space:pre-line;">
                        <?= htmlspecialchars($project['description']) ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <div class="list-item">
                <div class="list-item-body">
                    <div class="list-item-sub">Créé le</div>
                    <div class="list-item-title"><?= htmlspecialchars($createdAt) ?></div>
                </div>
            </div>
            <div class="list-item">
                <div class="list-item-body">
                    <div class="list-item-sub">Statut</div>
                    <div class="list-item-title" style="margin-top:4px;">
                        <?php if ($project['is_read_only']): ?>
                        <span class="badge muted">Archivé</span>
                        <?php else: ?>
                        <span class="badge green">Actif</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="list-item">
                <div class="list-item-body">
                    <div class="list-item-sub">Bugs</div>
                    <div class="list-item-title" style="display:flex;gap:6px;flex-wrap:wrap;margin-top:4px;">
                        <span class="badge red"><?= $bugCounts[BugStatusModel::OPEN] ?> ouverts</span>
                        <span class="badge orange"><?= $bugCounts[BugStatusModel::IN_PROGRESS] ?> en cours</span>
                        <span class="badge yellow"><?= $bugCounts[BugStatusModel::WAITING] ?> en attente</span>
                        <span class="badge green"><?= $bugCounts[BugStatusModel::CLOSED] ?> fermés</span>
                    </div>
                </div>
            </div>
        </div>

        <hr style="border:none;border-top:1px solid var(--border);margin:8px 0;">

        <div style="display:flex;flex-direction:column;gap:8px;padding-top:8px;">
            <form method="POST" action="/projects/toggleReadOnly/<?= (int) $project['id'] ?>">
                <button type="submit" class="table-btn <?= $project['is_read_only'] ? 'green' : 'orange' ?>"
                        style="width:100%;justify-content:center;">
                    <i class="ti ti-<?= $project['is_read_only'] ? 'lock-open' : 'archive' ?>"></i>
                    <?= $project['is_read_only'] ? 'Désarchiver le projet' : 'Archiver le projet' ?>
                </button>
            </form>
            <form method="POST" action="/projects/delete/<?= (int) $project['id'] ?>"
                  onsubmit="return confirm('Supprimer définitivement ce projet ?')">
                <button type="submit" class="table-btn danger" style="width:100%;justify-content:center;">
                    <i class="ti ti-trash"></i> Supprimer le projet
                </button>
            </form>
        </div>
    </div>

    <!-- Ajouter un membre -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">Ajouter un membre</span>
        </div>
        <?php if (!empty($nonMembers)): ?>
        <form method="POST" action="/projects/addMember/<?= (int) $project['id'] ?>">
            <div class="field" style="margin-bottom:8px;">
                <label for="user_id">Utilisateur</label>
                <select id="user_id" name="user_id">
                    <option value="">— Sélectionner un utilisateur —</option>
                    <?php foreach ($nonMembers as $u): ?>
                    <option value="<?= (int) $u['id'] ?>">
                        <?= htmlspecialchars($u['username']) ?>
                        <?php if (!empty($u['first_name']) || !empty($u['last_name'])): ?>
                            (<?= htmlspecialchars(trim($u['first_name'] . ' ' . $u['last_name'])) ?>)
                        <?php endif; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn-save" style="width:100%;justify-content:center;">
                <i class="ti ti-user-plus"></i> Ajouter au projet
            </button>
        </form>
        <?php else: ?>
        <p style="font-size:12px;color:var(--muted);text-align:center;padding:16px 0;">
            Tous les utilisateurs sont déjà membres de ce projet.
        </p>
        <?php endif; ?>
    </div>

</div>

<!-- Membres -->
<div class="card" style="margin-bottom:16px;">
    <div class="card-header">
        <span class="card-title">Membres du projet</span>
    </div>
    <?php if (empty($members)): ?>
    <div class="table-empty">Aucun membre assigné à ce projet.</div>
    <?php else: ?>
    <div class="table-wrap">
        <div class="table">
            <div class="row header">
                <div class="cell shrink">#</div>
                <div class="cell">Utilisateur</div>
                <div class="cell">Email</div>
                <div class="cell">Rôle</div>
                <div class="cell shrink">Statut</div>
                <div class="cell actions">Actions</div>
            </div>
            <?php foreach ($members as $m):
                $color    = $avatarColors[$m['id'] % 6];
                $i1       = !empty($m['first_name']) ? strtoupper($m['first_name'][0]) : '';
                $i2       = !empty($m['last_name'])  ? strtoupper($m['last_name'][0])  : '';
                $initials = $i1 . $i2 ?: strtoupper(substr($m['username'], 0, 2));
            ?>
            <div class="row">
                <div class="cell shrink muted"><?= (int) $m['id'] ?></div>
                <div class="cell">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <div class="avatar" style="background:<?= htmlspecialchars($color) ?>;">
                            <?= htmlspecialchars($initials) ?>
                        </div>
                        <div>
                            <div style="font-weight:500;font-size:13px;">
                                <a href="/users/view/<?= (int) $m['id'] ?>" style="color:var(--text);text-decoration:none;">
                                    <?= htmlspecialchars($m['username']) ?>
                                </a>
                            </div>
                            <?php if (!empty($m['first_name']) || !empty($m['last_name'])): ?>
                            <div style="font-size:11px;color:var(--muted);">
                                <?= htmlspecialchars(trim($m['first_name'] . ' ' . $m['last_name'])) ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="cell muted"><?= htmlspecialchars($m['email']) ?></div>
                <div class="cell">
                    <?php if (!empty($m['role_name'])): ?>
                    <span style="
                        display:inline-flex;align-items:center;font-size:10px;font-weight:500;
                        padding:2px 8px;border-radius:20px;
                        background:<?= htmlspecialchars($m['role_color']) ?>22;
                        color:<?= htmlspecialchars($m['role_color']) ?>;
                        border:1px solid <?= htmlspecialchars($m['role_color']) ?>55;
                    "><?= htmlspecialchars($m['role_name']) ?></span>
                    <?php else: ?>
                    <span class="badge muted">—</span>
                    <?php endif; ?>
                </div>
                <div class="cell shrink">
                    <?php if ($m['is_active']): ?>
                    <span class="badge green">Actif</span>
                    <?php else: ?>
                    <span class="badge red">Inactif</span>
                    <?php endif; ?>
                </div>
                <div class="cell actions">
                    <a href="/users/view/<?= (int) $m['id'] ?>" class="table-btn">
                        <i class="ti ti-eye"></i>
                    </a>
                    <form method="POST" action="/projects/removeMember/<?= (int) $project['id'] ?>/<?= (int) $m['id'] ?>"
                          style="display:inline;"
                          onsubmit="return confirm('Retirer <?= htmlspecialchars(addslashes($m['username'])) ?> du projet ?')">
                        <button type="submit" class="table-btn danger">
                            <i class="ti ti-user-minus"></i>
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Bugs récents -->
<?php if (!empty($recentBugs)): ?>
<div class="card">
    <div class="card-header">
        <span class="card-title">Bugs récents</span>
        <a href="/projects/bugs/<?= (int) $project['id'] ?>" class="btn-secondary" style="font-size:12px;">
            Voir tous les bugs
        </a>
    </div>
    <div class="list">
        <?php foreach ($recentBugs as $bug):
            $cls     = BugStatusModel::cssClass((int) $bug['status']);
            $label   = BugStatusModel::label((int) $bug['status']);
            $bugDate = !empty($bug['created_at']) ? date('d/m/Y', (int) $bug['created_at']) : '—';
        ?>
        <div class="list-item">
            <div class="list-dot <?= $cls ?>"></div>
            <div class="list-item-body">
                <div class="list-item-title"><?= htmlspecialchars($bug['title']) ?></div>
                <div class="list-item-sub">Signalé par <?= htmlspecialchars($bug['reporter'] ?? '—') ?></div>
            </div>
            <div class="list-item-end">
                <span class="badge <?= $cls ?>"><?= $label ?></span>
                <span class="list-item-time" style="margin-top:4px;"><?= $bugDate ?></span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>
