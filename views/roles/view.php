<!-- PAGE HEADER -->
<div class="page-header">
    <h1>Fiche rôle</h1>
    <p class="page-sub">Permissions et utilisateurs associés</p>
    <div class="page-header-actions">
        <a href="/roles/edit/<?= (int)($role['id'] ?? 0) ?>" class="btn btn-secondary">
            <i class="ti ti-edit"></i> Modifier
        </a>
        <a href="/roles/list" class="btn btn-ghost">← Retour</a>
    </div>
</div>

<div class="row-2col">

    <div class="card">
        <div class="card-header">
            <span class="card-title">Permissions</span>
        </div>
        <div class="list">
            <!-- populated by controller -->
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">Utilisateurs avec ce rôle</span>
        </div>
        <div class="list">
            <!-- populated by controller -->
        </div>
    </div>

</div>
