<?php
use Models\Log\ActivityLogModel;

$icon  = ActivityLogModel::icon($log['action']);
$color = ActivityLogModel::color($log['action']);
$label = ActivityLogModel::label($log['action']);
$date  = date('d/m/Y à H:i:s', (int) $log['activity_date']);
?>

<!-- PAGE HEADER -->
<div class="page-header-row">
    <div class="page-header">
        <h1>Log <span class="muted" style="font-weight:400;">#<?= (int) $log['id'] ?></span></h1>
        <p class="page-sub"><?= htmlspecialchars($label) ?></p>
    </div>
    <div class="page-actions">
        <a href="/logs/list" class="btn-secondary">
            <i class="ti ti-arrow-left"></i> Retour
        </a>
    </div>
</div>

<div class="row-2col-equal" style="margin-bottom:16px;">

    <!-- Informations -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">Informations</span>
        </div>
        <div class="list">
            <div class="list-item">
                <div class="list-item-body">
                    <div class="list-item-sub">Action</div>
                    <div class="list-item-title" style="margin-top:4px;">
                        <span class="badge <?= $color ?>">
                            <i class="ti <?= $icon ?>" style="margin-right:4px;"></i>
                            <?= htmlspecialchars($label) ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="list-item">
                <div class="list-item-body">
                    <div class="list-item-sub">Utilisateur</div>
                    <div class="list-item-title">
                        <?php if ($log['user_id']): ?>
                        <a href="/users/view/<?= (int) $log['user_id'] ?>"
                           style="color:var(--text);text-decoration:none;font-weight:500;">
                            <?= htmlspecialchars($log['username'] ?? '—') ?>
                        </a>
                        <?php else: ?>
                        <span class="muted">—</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="list-item">
                <div class="list-item-body">
                    <div class="list-item-sub">Date</div>
                    <div class="list-item-title"><?= htmlspecialchars($date) ?></div>
                </div>
            </div>
            <div class="list-item">
                <div class="list-item-body">
                    <div class="list-item-sub">Identifiant interne</div>
                    <div class="list-item-title muted" style="font-size:12px;"><?= htmlspecialchars($log['action']) ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Valeurs -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">Valeurs</span>
        </div>
        <div class="list">
            <div class="list-item" style="flex-direction:column;align-items:flex-start;gap:6px;">
                <div class="list-item-sub">Valeur courante</div>
                <?php if ($log['current'] !== null && $log['current'] !== ''): ?>
                <div style="background:var(--surface-2,#f3f4f6);border-radius:6px;padding:10px 14px;
                            font-size:13px;width:100%;box-sizing:border-box;word-break:break-word;">
                    <?= htmlspecialchars($log['current']) ?>
                </div>
                <?php else: ?>
                <span class="muted" style="font-size:13px;">—</span>
                <?php endif; ?>
            </div>
            <?php if ($log['previous'] !== null && $log['previous'] !== ''): ?>
            <div class="list-item" style="flex-direction:column;align-items:flex-start;gap:6px;">
                <div class="list-item-sub">Valeur précédente</div>
                <div style="background:var(--surface-2,#f3f4f6);border-radius:6px;padding:10px 14px;
                            font-size:13px;width:100%;box-sizing:border-box;word-break:break-word;
                            color:var(--muted);text-decoration:line-through;">
                    <?= htmlspecialchars($log['previous']) ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

</div>
