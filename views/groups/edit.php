<?php
$hasErrors  = !empty($messages) && is_array($messages) && empty($messages['success']);
$hasSuccess = !empty($messages['success']);
?>

<!-- PAGE HEADER -->
<div class="page-header-row">
    <div class="page-header">
        <h1>Modifier <?= htmlspecialchars($group['name']) ?></h1>
        <p class="page-sub">Mise à jour des informations du groupe</p>
    </div>
    <div class="page-actions">
        <a href="/groups/view/<?= (int) $group['id'] ?>" class="table-btn">
            <i class="ti ti-arrow-left"></i> Fiche groupe
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Informations du groupe</span>
    </div>

    <?php if ($hasSuccess): ?>
    <div class="alert success" style="margin:16px 0 0;">
        <i class="ti ti-check"></i> Les modifications ont été enregistrées.
    </div>
    <?php elseif ($hasErrors): ?>
    <div class="alert error" style="margin:16px 0 0;">
        <i class="ti ti-alert-circle"></i>
        Veuillez corriger les erreurs ci-dessous avant de continuer.
    </div>
    <?php endif; ?>

    <form method="POST" action="/groups/edit/<?= (int) $group['id'] ?>">

        <p class="form-section-title">Identité</p>

        <div class="field <?= !empty($messages['name']) ? 'has-error' : '' ?>">
            <label for="name">Nom du groupe <span style="color:var(--danger)">*</span></label>
            <input type="text" id="name" name="name" required
                   value="<?= htmlspecialchars($group['name'] ?? '') ?>">
            <?php foreach ($messages['name'] ?? [] as $err): ?>
            <div class="field-error"><?= htmlspecialchars($err) ?></div>
            <?php endforeach; ?>
        </div>

        <div class="field">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3"><?= htmlspecialchars($group['description'] ?? '') ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-save">
                <i class="ti ti-device-floppy"></i> Enregistrer
            </button>
            <a href="/groups/view/<?= (int) $group['id'] ?>" class="btn-secondary">Annuler</a>
            <a href="/groups/list" class="btn-secondary">Liste des groupes</a>
        </div>

    </form>
</div>
