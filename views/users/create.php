<?php
$hasErrors = !empty($messages) && is_array($messages);
?>

<!-- PAGE HEADER -->
<div class="page-header-row">
    <div class="page-header">
        <h1>Nouvel utilisateur</h1>
        <p class="page-sub">Créer un nouveau compte utilisateur</p>
    </div>
    <div class="page-actions">
        <a href="/users/list" class="table-btn">
            <i class="ti ti-arrow-left"></i> Retour
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Informations du compte</span>
    </div>

    <?php if ($hasErrors): ?>
    <div class="alert error" style="margin:16px 0 0;">
        <i class="ti ti-alert-circle"></i>
        Veuillez corriger les erreurs ci-dessous avant de continuer.
    </div>
    <?php endif; ?>

    <form method="POST" action="/users/create">

        <p class="form-section-title">Identité</p>

        <div class="field-row">
            <div class="field">
                <label for="first_name">Prénom</label>
                <input type="text" id="first_name" name="first_name"
                       value="<?= htmlspecialchars($input['first_name']) ?>">
            </div>
            <div class="field">
                <label for="last_name">Nom</label>
                <input type="text" id="last_name" name="last_name"
                       value="<?= htmlspecialchars($input['last_name']) ?>">
            </div>
        </div>

        <div class="field-row">
            <div class="field <?= !empty($messages['username']) ? 'has-error' : '' ?>">
                <label for="username">Nom d'utilisateur <span style="color:var(--danger)">*</span></label>
                <input type="text" id="username" name="username" required
                       value="<?= htmlspecialchars($input['username']) ?>">
                <?php foreach ($messages['username'] ?? [] as $err): ?>
                <div class="field-error"><?= htmlspecialchars($err) ?></div>
                <?php endforeach; ?>
            </div>
            <div class="field <?= !empty($messages['email']) ? 'has-error' : '' ?>">
                <label for="email">Adresse e-mail <span style="color:var(--danger)">*</span></label>
                <input type="email" id="email" name="email" required
                       value="<?= htmlspecialchars($input['email']) ?>">
                <?php foreach ($messages['email'] ?? [] as $err): ?>
                <div class="field-error"><?= htmlspecialchars($err) ?></div>
                <?php endforeach; ?>
            </div>
        </div>

        <p class="form-section-title">Mot de passe</p>

        <div class="field-row">
            <div class="field <?= !empty($messages['password']) ? 'has-error' : '' ?>">
                <label for="password">Mot de passe <span style="color:var(--danger)">*</span></label>
                <input type="password" id="password" name="password" required autocomplete="new-password">
                <div class="field-hint">Minimum 12 caractères.</div>
                <?php foreach ($messages['password'] ?? [] as $err): ?>
                <div class="field-error"><?= htmlspecialchars($err) ?></div>
                <?php endforeach; ?>
            </div>
            <div class="field <?= !empty($messages['confirm']) ? 'has-error' : '' ?>">
                <label for="confirm_password">Confirmer le mot de passe <span style="color:var(--danger)">*</span></label>
                <input type="password" id="confirm_password" name="confirm_password" required autocomplete="new-password">
                <?php foreach ($messages['confirm'] ?? [] as $err): ?>
                <div class="field-error"><?= htmlspecialchars($err) ?></div>
                <?php endforeach; ?>
            </div>
        </div>

        <p class="form-section-title">Accès &amp; Statut</p>

        <div class="field-row">
            <div class="field">
                <label for="role_id">Rôle</label>
                <select id="role_id" name="role_id">
                    <option value="">— Aucun rôle —</option>
                    <?php foreach ($roles as $role): ?>
                    <option value="<?= (int) $role['id'] ?>"
                        <?= (int) $input['role_id'] === (int) $role['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($role['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field">
                <label for="is_active">Statut du compte</label>
                <select id="is_active" name="is_active">
                    <option value="1" <?= (int) $input['is_active'] === 1 ? 'selected' : '' ?>>Actif</option>
                    <option value="0" <?= (int) $input['is_active'] === 0 ? 'selected' : '' ?>>Inactif</option>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-save">
                <i class="ti ti-user-plus"></i> Créer l'utilisateur
            </button>
            <a href="/users/list" class="btn-secondary">Annuler</a>
        </div>

    </form>
</div>
