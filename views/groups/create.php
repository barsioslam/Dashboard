<?php
$hasErrors = !empty($messages) && is_array($messages);
?>

<!-- PAGE HEADER -->
<div class="page-header-row">
    <div class="page-header">
        <h1>Nouveau groupe</h1>
        <p class="page-sub">Créer un groupe d'utilisateurs</p>
    </div>
    <div class="page-actions">
        <a href="/groups/list" class="table-btn">
            <i class="ti ti-arrow-left"></i> Retour
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Informations du groupe</span>
    </div>

    <?php if ($hasErrors): ?>
    <div class="alert error" style="margin:16px 0 0;">
        <i class="ti ti-alert-circle"></i>
        Veuillez corriger les erreurs ci-dessous avant de continuer.
    </div>
    <?php endif; ?>

    <form method="POST" action="/groups/create">

        <p class="form-section-title">Identité</p>

        <div class="field <?= !empty($messages['name']) ? 'has-error' : '' ?>">
            <label for="name">Nom du groupe <span style="color:var(--danger)">*</span></label>
            <input type="text" id="name" name="name" required
                   value="<?= htmlspecialchars($input['name']) ?>" placeholder="Ex : Équipe développement">
            <?php foreach ($messages['name'] ?? [] as $err): ?>
            <div class="field-error"><?= htmlspecialchars($err) ?></div>
            <?php endforeach; ?>
        </div>

        <div class="field">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3"
                      placeholder="Description optionnelle du groupe…"><?= htmlspecialchars($input['description']) ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-save">
                <i class="ti ti-users-group"></i> Créer le groupe
            </button>
            <a href="/groups/list" class="btn-secondary">Annuler</a>
        </div>

    </form>
</div>
