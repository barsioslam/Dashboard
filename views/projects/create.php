<?php
$hasErrors = !empty($messages) && is_array($messages);
?>

<!-- PAGE HEADER -->
<div class="page-header-row">
    <div class="page-header">
        <h1>Nouveau projet</h1>
        <p class="page-sub">Créer un nouveau projet</p>
    </div>
    <div class="page-actions">
        <a href="/projects/list" class="table-btn">
            <i class="ti ti-arrow-left"></i> Retour
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Informations du projet</span>
    </div>

    <?php if ($hasErrors): ?>
    <div class="alert error" style="margin:16px 0 0;">
        <i class="ti ti-alert-circle"></i>
        Veuillez corriger les erreurs ci-dessous avant de continuer.
    </div>
    <?php endif; ?>

    <form method="POST" action="/projects/create">

        <p class="form-section-title">Identité</p>

        <div class="field <?= !empty($messages['name']) ? 'has-error' : '' ?>">
            <label for="name">Nom du projet <span style="color:var(--danger)">*</span></label>
            <input type="text" id="name" name="name" required
                   value="<?= htmlspecialchars($input['name']) ?>"
                   placeholder="Ex : Refonte site web">
            <?php foreach ($messages['name'] ?? [] as $err): ?>
            <div class="field-error"><?= htmlspecialchars($err) ?></div>
            <?php endforeach; ?>
        </div>

        <div class="field">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4"
                      placeholder="Description optionnelle du projet…"><?= htmlspecialchars($input['description']) ?></textarea>
        </div>

        <p class="form-section-title">Options</p>

        <div class="field">
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:normal;">
                <input type="checkbox" name="is_read_only" value="1"
                    <?= $input['is_read_only'] ? 'checked' : '' ?>>
                Archivé (lecture seule)
            </label>
            <div class="field-hint">Un projet archivé reste visible mais n'accepte plus de modifications.</div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-save">
                <i class="ti ti-folder-plus"></i> Créer le projet
            </button>
            <a href="/projects/list" class="btn-secondary">Annuler</a>
        </div>

    </form>
</div>
