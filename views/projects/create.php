<!-- PAGE HEADER -->
<div class="page-header">
    <h1>Nouveau projet</h1>
    <p class="page-sub">Créer un nouveau projet</p>
    <a href="/projects/list" class="btn btn-ghost">← Retour</a>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Informations du projet</span>
    </div>
    <form method="POST" action="/projects/create" class="form">
        <div class="form-group">
            <label for="name">Nom du projet</label>
            <input type="text" id="name" name="name" class="form-input" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" class="form-input" rows="4"></textarea>
        </div>
        <div class="form-group">
            <label class="form-check">
                <input type="checkbox" name="is_read_only" value="1">
                Archivé (lecture seule)
            </label>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Créer</button>
            <a href="/projects/list" class="btn btn-ghost">Annuler</a>
        </div>
    </form>
</div>
