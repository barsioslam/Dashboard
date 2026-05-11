<!-- PAGE HEADER -->
<div class="page-header">
    <h1>Bugs du projet</h1>
    <p class="page-sub">Liste des bugs signalés</p>
    <a href="/projects/view/<?= (int)($project['id'] ?? 0) ?>" class="btn btn-ghost">← Retour projet</a>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Bugs signalés</span>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Titre</th>
                <th>Signalé par</th>
                <th>Signalé le</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- populated by controller -->
        </tbody>
    </table>
</div>
