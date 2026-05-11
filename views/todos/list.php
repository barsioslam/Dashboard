<!-- PAGE HEADER -->
<div class="page-header">
    <h1>Todo Lists</h1>
    <p class="page-sub">Gestion des listes de tâches</p>
    <a href="/todos/create" class="btn btn-primary">
        <i class="ti ti-list-check"></i> Nouvelle todo list
    </a>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Toutes les todo lists</span>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Titre</th>
                <th>Tâches</th>
                <th>Terminées</th>
                <th>Propriétaire</th>
                <th>Créée le</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- populated by controller -->
        </tbody>
    </table>
</div>
