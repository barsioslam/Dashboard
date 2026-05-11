<!-- PAGE HEADER -->
<div class="page-header">
    <h1>Détail du log</h1>
    <p class="page-sub">Informations complètes sur cette entrée</p>
    <a href="/logs/list" class="btn btn-ghost">← Retour</a>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Entrée de log</span>
    </div>
    <div class="detail-list">
        <!-- populated by controller -->
    </div>
</div>

<div class="row-2col">

    <div class="card">
        <div class="card-header">
            <span class="card-title">Valeur précédente</span>
        </div>
        <pre class="code-block"><?= htmlspecialchars($log['previous'] ?? '—') ?></pre>
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">Valeur actuelle</span>
        </div>
        <pre class="code-block"><?= htmlspecialchars($log['current'] ?? '—') ?></pre>
    </div>

</div>
