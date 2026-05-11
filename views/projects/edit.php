<!-- PAGE HEADER -->
<div class="page-header">
    <h1>Modifier projet</h1>
    <p class="page-sub">Mise à jour des informations du projet</p>
    <a href="/projects/list" class="btn btn-ghost">← Retour</a>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Informations du projet</span>
    </div>
    <form method="POST" action="/projects/edit/<?= (int)($project['id'] ?? 0) ?>" class="form">
        <div class="form-group">
            <label for="name">Nom du projet</label>
            <input type="text" id="name" name="name" class="form-input"
                   value="<?= htmlspecialchars($project['name'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" class="form-input" rows="4"><?= htmlspecialchars($project['description'] ?? '') ?></textarea>
        </div>
        <div class="form-group">
            <label class="form-check">
                <input type="checkbox" name="is_read_only" value="1"
                    <?= !empty($project['is_read_only']) ? 'checked' : '' ?>>
                Archivé (lecture seule)
            </label>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="/projects/list" class="btn btn-ghost">Annuler</a>
        </div>
    </form>
</div>
