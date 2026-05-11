<?php
$avatarColors = ['#4f8ef7','#7c5cfc','#22c97a','#f5a623','#f25f5c','#0ea5e9'];
$createdAt = !empty($group['created_at']) ? date('d/m/Y à H:i', (int) $group['created_at']) : '—';
?>

<!-- PAGE HEADER -->
<div class="page-header-row">
    <div class="page-header">
        <h1><?= htmlspecialchars($group['name']) ?></h1>
        <p class="page-sub">Fiche groupe</p>
    </div>
    <div class="page-actions">
        <a href="/groups/edit/<?= (int) $group['id'] ?>" class="btn-save">
            <i class="ti ti-edit"></i> Modifier
        </a>
        <a href="/groups/list" class="btn-secondary">
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
                    <div class="list-item-sub">Nom du groupe</div>
                    <div class="list-item-title"><?= htmlspecialchars($group['name']) ?></div>
                </div>
            </div>
            <div class="list-item">
                <div class="list-item-body">
                    <div class="list-item-sub">Description</div>
                    <div class="list-item-title">
                        <?= !empty($group['description']) ? htmlspecialchars($group['description']) : '<span style="color:var(--muted)">—</span>' ?>
                    </div>
                </div>
            </div>
            <div class="list-item">
                <div class="list-item-body">
                    <div class="list-item-sub">Créé le</div>
                    <div class="list-item-title"><?= htmlspecialchars($createdAt) ?></div>
                </div>
            </div>
            <div class="list-item">
                <div class="list-item-body">
                    <div class="list-item-sub">Nombre de membres</div>
                    <div class="list-item-title"><?= count($members) ?> membre<?= count($members) !== 1 ? 's' : '' ?></div>
                </div>
            </div>
        </div>

        <hr style="border:none;border-top:1px solid var(--border);margin:8px 0;">

        <div style="padding-top:8px;">
            <form method="POST" action="/groups/delete/<?= (int) $group['id'] ?>"
                  onsubmit="return confirm('Supprimer définitivement ce groupe et tous ses membres ?')">
                <button type="submit" class="table-btn danger" style="width:100%;justify-content:center;">
                    <i class="ti ti-trash"></i> Supprimer le groupe
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
        <form method="POST" action="/groups/addMember/<?= (int) $group['id'] ?>">
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
                <i class="ti ti-user-plus"></i> Ajouter au groupe
            </button>
        </form>
        <?php else: ?>
        <p style="font-size:12px;color:var(--muted);text-align:center;padding:16px 0;">
            Tous les utilisateurs sont déjà membres de ce groupe.
        </p>
        <?php endif; ?>
    </div>

</div>

<!-- Membres -->
<div class="card">
    <div class="card-header">
        <span class="card-title">Membres du groupe</span>
    </div>

    <?php if (empty($members)): ?>
    <div class="table-empty">Aucun membre dans ce groupe.</div>
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
                        display:inline-flex;align-items:center;
                        font-size:10px;font-weight:500;padding:2px 8px;border-radius:20px;
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
                    <form method="POST" action="/groups/removeMember/<?= (int) $group['id'] ?>/<?= (int) $m['id'] ?>"
                          style="display:inline;"
                          onsubmit="return confirm('Retirer <?= htmlspecialchars(addslashes($m['username'])) ?> du groupe ?')">
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
