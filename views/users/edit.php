<?php
$hasErrors  = !empty($messages) && is_array($messages) && empty($messages['success']);
$hasSuccess = !empty($messages['success']);
?>

<!-- PAGE HEADER -->
<div class="page-header-row">
    <div class="page-header">
        <h1>Modifier <?= htmlspecialchars($user['username']) ?></h1>
        <p class="page-sub">Mise à jour des informations du compte</p>
    </div>
    <div class="page-actions">
        <a href="/users/view/<?= (int) $user['id'] ?>" class="table-btn">
            <i class="ti ti-arrow-left"></i> Fiche utilisateur
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Informations du compte</span>
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

    <form method="POST" action="/users/edit/<?= (int) $user['id'] ?>">

        <p class="form-section-title">Identité</p>

        <div class="field-row">
            <div class="field">
                <label for="first_name">Prénom</label>
                <input type="text" id="first_name" name="first_name"
                       value="<?= htmlspecialchars($user['first_name'] ?? '') ?>">
            </div>
            <div class="field">
                <label for="last_name">Nom</label>
                <input type="text" id="last_name" name="last_name"
                       value="<?= htmlspecialchars($user['last_name'] ?? '') ?>">
            </div>
        </div>

        <div class="field-row">
            <div class="field <?= !empty($messages['username']) ? 'has-error' : '' ?>">
                <label for="username">Nom d'utilisateur <span style="color:var(--danger)">*</span></label>
                <input type="text" id="username" name="username" required
                       value="<?= htmlspecialchars($user['username'] ?? '') ?>">
                <?php foreach ($messages['username'] ?? [] as $err): ?>
                <div class="field-error"><?= htmlspecialchars($err) ?></div>
                <?php endforeach; ?>
            </div>
            <div class="field <?= !empty($messages['email']) ? 'has-error' : '' ?>">
                <label for="email">Adresse e-mail <span style="color:var(--danger)">*</span></label>
                <input type="email" id="email" name="email" required
                       value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                <?php foreach ($messages['email'] ?? [] as $err): ?>
                <div class="field-error"><?= htmlspecialchars($err) ?></div>
                <?php endforeach; ?>
            </div>
        </div>

        <p class="form-section-title">Nouveau mot de passe</p>

        <div class="field-row">
            <div class="field <?= !empty($messages['new_password']) ? 'has-error' : '' ?>">
                <label for="new_password">Nouveau mot de passe</label>
                <input type="password" id="new_password" name="new_password" autocomplete="new-password">
                <div class="field-hint">Laisser vide pour ne pas modifier. Minimum 12 caractères si renseigné.</div>
                <?php foreach ($messages['new_password'] ?? [] as $err): ?>
                <div class="field-error"><?= htmlspecialchars($err) ?></div>
                <?php endforeach; ?>
            </div>
        </div>

        <p class="form-section-title">Accès &amp; Statut</p>

        <div class="field-row">
            <div class="field">
                <label for="role_id">Rôle</label>
                <select id="role_id" name="role_id">
                    <option value="0">— Aucun rôle —</option>
                    <?php foreach ($roles as $role): ?>
                    <option value="<?= (int) $role['id'] ?>"
                        <?= (int) ($user['role_id'] ?? 0) === (int) $role['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($role['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field">
                <label for="is_active">Statut du compte</label>
                <select id="is_active" name="is_active">
                    <option value="1" <?= (int) ($user['is_active'] ?? 1) === 1 ? 'selected' : '' ?>>Actif</option>
                    <option value="0" <?= (int) ($user['is_active'] ?? 1) === 0 ? 'selected' : '' ?>>Inactif</option>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-save">
                <i class="ti ti-device-floppy"></i> Enregistrer
            </button>
            <a href="/users/view/<?= (int) $user['id'] ?>" class="btn-secondary">Annuler</a>
            <a href="/users/list" class="btn-secondary">Liste des utilisateurs</a>
        </div>

    </form>
</div>
