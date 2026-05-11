<!-- PAGE HEADER -->
<div class="page-header">
    <h1>Nouveau groupe</h1>
    <p class="page-sub">Créer un groupe d'utilisateurs</p>
    <a href="/groups/list" class="btn btn-ghost">← Retour</a>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Informations du groupe</span>
    </div>
    <form method="POST" action="/groups/create" class="form">
        <div class="form-group">
            <label for="name">Nom du groupe</label>
            <input type="text" id="name" name="name" class="form-input" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" class="form-input" rows="3"></textarea>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Créer</button>
            <a href="/groups/list" class="btn btn-ghost">Annuler</a>
        </div>
    </form>
</div>
