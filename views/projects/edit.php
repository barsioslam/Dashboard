<?php
$hasErrors  = !empty($messages) && is_array($messages) && empty($messages['success']);
$hasSuccess = !empty($messages['success']);
?>

<!-- PAGE HEADER -->
<div class="page-header-row">
    <div class="page-header">
        <h1>Modifier <?= htmlspecialchars($project['name']) ?></h1>
        <p class="page-sub">Mise à jour des informations du projet</p>
    </div>
    <div class="page-actions">
        <a href="/projects/view/<?= (int) $project['id'] ?>" class="table-btn">
            <i class="ti ti-arrow-left"></i> Fiche projet
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Informations du projet</span>
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

    <form method="POST" action="/projects/edit/<?= (int) $project['id'] ?>">

        <p class="form-section-title">Identité</p>

        <div class="field <?= !empty($messages['name']) ? 'has-error' : '' ?>">
            <label for="name">Nom du projet <span style="color:var(--danger)">*</span></label>
            <input type="text" id="name" name="name" required
                   value="<?= htmlspecialchars($project['name'] ?? '') ?>">
            <?php foreach ($messages['name'] ?? [] as $err): ?>
            <div class="field-error"><?= htmlspecialchars($err) ?></div>
            <?php endforeach; ?>
        </div>

        <div class="field">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4"><?= htmlspecialchars($project['description'] ?? '') ?></textarea>
        </div>

        <p class="form-section-title">Options</p>

        <div class="field">
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:normal;">
                <input type="checkbox" name="is_read_only" value="1"
                    <?= (int) ($project['is_read_only'] ?? 0) ? 'checked' : '' ?>>
                Archivé (lecture seule)
            </label>
            <div class="field-hint">Un projet archivé reste visible mais n'accepte plus de modifications.</div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-save">
                <i class="ti ti-device-floppy"></i> Enregistrer
            </button>
            <a href="/projects/view/<?= (int) $project['id'] ?>" class="btn-secondary">Annuler</a>
            <a href="/projects/list" class="btn-secondary">Liste des projets</a>
        </div>

    </form>
</div>
