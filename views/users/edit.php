<!-- PAGE HEADER -->
<div class="page-header">
    <h1>Modifier utilisateur</h1>
    <p class="page-sub">Mise à jour des informations du compte</p>
    <a href="/users/list" class="btn btn-ghost">← Retour</a>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Informations du compte</span>
    </div>
    <form method="POST" action="/users/edit/<?= (int)($user['id'] ?? 0) ?>" class="form">
        <div class="form-row">
            <div class="form-group">
                <label for="username">Nom d'utilisateur</label>
                <input type="text" id="username" name="username" class="form-input"
                       value="<?= htmlspecialchars($user['username'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" class="form-input"
                       value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="first_name">Prénom</label>
                <input type="text" id="first_name" name="first_name" class="form-input"
                       value="<?= htmlspecialchars($user['first_name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="last_name">Nom</label>
                <input type="text" id="last_name" name="last_name" class="form-input"
                       value="<?= htmlspecialchars($user['last_name'] ?? '') ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="role">Rôle</label>
            <select id="role" name="role_id" class="form-input">
                <!-- options populated by controller -->
            </select>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="/users/list" class="btn btn-ghost">Annuler</a>
        </div>
    </form>
</div>
