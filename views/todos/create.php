<!-- PAGE HEADER -->
<div class="page-header">
    <h1>Nouvelle todo list</h1>
    <p class="page-sub">Créer une liste de tâches</p>
    <a href="/todos/list" class="btn btn-ghost">← Retour</a>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Informations</span>
    </div>
    <form method="POST" action="/todos/create" class="form">
        <div class="form-group">
            <label for="title">Titre</label>
            <input type="text" id="title" name="title" class="form-input" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" class="form-input" rows="3"></textarea>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Créer</button>
            <a href="/todos/list" class="btn btn-ghost">Annuler</a>
        </div>
    </form>
</div>
