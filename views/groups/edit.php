<!-- PAGE HEADER -->
<div class="page-header">
    <h1>Modifier groupe</h1>
    <p class="page-sub">Mise à jour des informations du groupe</p>
    <a href="/groups/list" class="btn btn-ghost">← Retour</a>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Informations du groupe</span>
    </div>
    <form method="POST" action="/groups/edit/<?= (int)($group['id'] ?? 0) ?>" class="form">
        <div class="form-group">
            <label for="name">Nom du groupe</label>
            <input type="text" id="name" name="name" class="form-input"
                   value="<?= htmlspecialchars($group['name'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" class="form-input" rows="3"><?= htmlspecialchars($group['description'] ?? '') ?></textarea>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="/groups/list" class="btn btn-ghost">Annuler</a>
        </div>
    </form>
</div>
