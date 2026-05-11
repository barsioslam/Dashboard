<!-- PAGE HEADER -->
<div class="page-header">
    <h1>Todo List</h1>
    <p class="page-sub">Détail et éléments de la liste</p>
    <div class="page-header-actions">
        <a href="/todos/edit/<?= (int)($todoList['id'] ?? 0) ?>" class="btn btn-secondary">
            <i class="ti ti-edit"></i> Modifier
        </a>
        <a href="/todos/list" class="btn btn-ghost">← Retour</a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Tâches</span>
    </div>
    <div class="list">
        <!-- populated by controller -->
    </div>
</div>
