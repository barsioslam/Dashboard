<!-- PAGE HEADER -->
<div class="page-header">
    <h1>Fiche projet</h1>
    <p class="page-sub">Détails, membres et bugs</p>
    <div class="page-header-actions">
        <a href="/projects/bugs/<?= (int)($project['id'] ?? 0) ?>" class="btn btn-secondary">
            <i class="ti ti-bug"></i> Bugs
        </a>
        <a href="/projects/edit/<?= (int)($project['id'] ?? 0) ?>" class="btn btn-secondary">
            <i class="ti ti-edit"></i> Modifier
        </a>
        <a href="/projects/list" class="btn btn-ghost">← Retour</a>
    </div>
</div>

<div class="row-2col">

    <div class="card">
        <div class="card-header">
            <span class="card-title">Informations</span>
        </div>
        <div class="detail-list">
            <!-- populated by controller -->
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">Membres</span>
        </div>
        <div class="list">
            <!-- populated by controller -->
        </div>
    </div>

</div>
