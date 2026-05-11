<!-- PAGE HEADER -->
<div class="page-header">
    <h1>Modifier rôle</h1>
    <p class="page-sub">Mise à jour du rôle et de ses permissions</p>
    <a href="/roles/list" class="btn btn-ghost">← Retour</a>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Informations du rôle</span>
    </div>
    <form method="POST" action="/roles/edit/<?= (int)($role['id'] ?? 0) ?>" class="form">
        <div class="form-row">
            <div class="form-group">
                <label for="name">Nom du rôle</label>
                <input type="text" id="name" name="name" class="form-input"
                       value="<?= htmlspecialchars($role['name'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="color">Couleur</label>
                <input type="text" id="color" name="color" class="form-input"
                       value="<?= htmlspecialchars($role['color'] ?? '') ?>">
            </div>
        </div>
        <div class="form-group">
            <label>Permissions</label>
            <div class="permissions-grid">
                <!-- checkboxes populated by controller -->
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="/roles/list" class="btn btn-ghost">Annuler</a>
        </div>
    </form>
</div>
