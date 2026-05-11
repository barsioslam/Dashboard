<!-- PAGE HEADER -->
<div class="page-header">
    <h1>Fiche notification</h1>
    <p class="page-sub">Détails de la notification</p>
    <div class="page-header-actions">
        <a href="/notifications/edit/<?= (int)($notification['id'] ?? 0) ?>" class="btn btn-secondary">
            <i class="ti ti-edit"></i> Modifier
        </a>
        <a href="/notifications/list" class="btn btn-ghost">← Retour</a>
    </div>
</div>

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
        <span class="card-title">Destinataires</span>
    </div>
    <div class="list">
        <!-- populated by controller -->
    </div>
</div>
