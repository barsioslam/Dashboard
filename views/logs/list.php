<?php
use Models\Log\ActivityLogModel;

$buildUrl = function(array $extra) use ($search, $action, $userId, $logspage): string {
    $params = array_filter(array_merge([
        'q'      => $search,
        'action' => $action,
        'user'   => $userId ?: '',
        'page'   => $logspage,
    ], $extra), fn($v) => $v !== '' && $v !== 0 && $v !== null);
    return '/logs/list' . ($params ? '?' . http_build_query($params) : '');
};
?>

<!-- PAGE HEADER -->
<div class="page-header-row">
    <div class="page-header">
        <h1>Logs d'activité</h1>
        <p class="page-sub"><?= number_format($total) ?> entrée<?= $total !== 1 ? 's' : '' ?></p>
    </div>
</div>

<!-- Filtres -->
<div class="card" style="margin-bottom:16px;">
    <form method="GET" action="/logs/list" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;padding:4px 0;">
        <div class="field" style="margin:0;flex:1;min-width:160px;">
            <label>Recherche</label>
            <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Utilisateur, valeur…">
        </div>
        <div class="field" style="margin:0;min-width:180px;">
            <label>Action</label>
            <select name="action">
                <option value="">— Toutes —</option>
                <?php foreach ($actions as $a): ?>
                <option value="<?= htmlspecialchars($a) ?>" <?= $action === $a ? 'selected' : '' ?>>
                    <?= htmlspecialchars(ActivityLogModel::label($a)) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field" style="margin:0;min-width:160px;">
            <label>Utilisateur</label>
            <select name="user">
                <option value="">— Tous —</option>
                <?php foreach ($users as $u): ?>
                <option value="<?= (int) $u['id'] ?>" <?= $userId === (int) $u['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($u['username']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="display:flex;gap:8px;">
            <button type="submit" class="btn-save"><i class="ti ti-filter"></i> Filtrer</button>
            <?php if ($search || $action || $userId): ?>
            <a href="/logs/list" class="btn-secondary"><i class="ti ti-x"></i> Réinitialiser</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Table -->
<div class="card">
    <div class="table-wrap">
        <div class="table">
            <div class="row header">
                <div class="cell shrink">#</div>
                <div class="cell">Action</div>
                <div class="cell">Utilisateur</div>
                <div class="cell">Valeur</div>
                <div class="cell">Précédent</div>
                <div class="cell shrink">Date</div>
                <div class="cell actions"></div>
            </div>

            <?php if (empty($logs)): ?>
            <div class="table-empty">Aucun log trouvé.</div>
            <?php else: foreach ($logs as $log):
                $icon  = ActivityLogModel::icon($log['action']);
                $color = ActivityLogModel::color($log['action']);
                $label = ActivityLogModel::label($log['action']);
                $date  = date('d/m/Y H:i', (int) $log['activity_date']);
            ?>
            <div class="row">
                <div class="cell shrink muted"><?= (int) $log['id'] ?></div>
                <div class="cell">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span class="badge <?= $color ?>" style="padding:4px 6px;">
                            <i class="ti <?= $icon ?>"></i>
                        </span>
                        <span style="font-size:13px;font-weight:500;"><?= htmlspecialchars($label) ?></span>
                    </div>
                </div>
                <div class="cell">
                    <?php if ($log['user_id']): ?>
                    <a href="/users/view/<?= (int) $log['user_id'] ?>"
                       style="font-size:13px;color:var(--text);text-decoration:none;font-weight:500;">
                        <?= htmlspecialchars($log['username'] ?? '—') ?>
                    </a>
                    <?php else: ?>
                    <span class="muted">—</span>
                    <?php endif; ?>
                </div>
                <div class="cell" style="font-size:12px;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                    <?= htmlspecialchars(mb_strimwidth($log['current'] ?? '', 0, 60, '…')) ?>
                </div>
                <div class="cell muted" style="font-size:12px;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                    <?= htmlspecialchars(mb_strimwidth($log['previous'] ?? '', 0, 40, '…')) ?>
                </div>
                <div class="cell shrink muted" style="font-size:12px;white-space:nowrap;"><?= $date ?></div>
                <div class="cell actions">
                    <a href="/logs/view/<?= (int) $log['id'] ?>" class="table-btn">
                        <i class="ti ti-eye"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; endif; ?>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($pages > 1): ?>
    <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-top:1px solid var(--border);">
        <span style="font-size:12px;color:var(--muted);">Page <?= $logspage ?> / <?= $pages ?></span>
        <div style="display:flex;gap:6px;">
            <?php if ($logspage > 1): ?>
            <a href="<?= $buildUrl(['page' => $logspage - 1]) ?>" class="table-btn">
                <i class="ti ti-chevron-left"></i>
            </a>
            <?php endif; ?>
            <?php foreach (range(max(1, $logspage - 2), min($pages, $logspage + 2)) as $p): ?>
            <a href="<?= $buildUrl(['page' => $p]) ?>"
               class="table-btn <?= $p === $logspage ? 'active' : '' ?>"
               style="<?= $p === $logspage ? 'background:var(--primary);color:#fff;' : '' ?>">
                <?= $p ?>
            </a>
            <?php endforeach; ?>
            <?php if ($logspage < $pages): ?>
            <a href="<?= $buildUrl(['page' => $logspage + 1]) ?>" class="table-btn">
                <i class="ti ti-chevron-right"></i>
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
