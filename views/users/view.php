<!-- PAGE HEADER -->
<div class="page-header">
    <h1>Fiche utilisateur</h1>
    <p class="page-sub">Détails, rôles et activité</p>
    <div class="page-header-actions">
        <a href="/users/edit/<?= (int)($user['id'] ?? 0) ?>" class="btn btn-secondary">
            <i class="ti ti-edit"></i> Modifier
        </a>
        <a href="/users/list" class="btn btn-ghost">← Retour</a>
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
            <span class="card-title">Rôles &amp; permissions</span>
        </div>
        <div class="list">
            <!-- populated by controller -->
        </div>
    </div>

</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Activité récente</span>
    </div>
    <div class="list">
        <!-- populated by controller -->
    </div>
</div>
