<?php
use App\Utils\Text\Text;

function timeAgo(int $timestamp): string {
    $diff = time() - $timestamp;
    if ($diff < 60)    return 'à l\'instant';
    if ($diff < 3600)  return 'il y a ' . floor($diff / 60) . ' min';
    if ($diff < 86400) return 'il y a ' . floor($diff / 3600) . 'h';
    return 'il y a ' . floor($diff / 86400) . 'j';
}

function getAvatarGradient(string $username): string {
    $gradients = [
        'linear-gradient(135deg,#7c5cfc,#4f8ef7)',
        'linear-gradient(135deg,#4f8ef7,#22c97a)',
        'linear-gradient(135deg,#22c97a,#4f8ef7)',
        'linear-gradient(135deg,#f5a623,#f25f5c)',
        'linear-gradient(135deg,#f25f5c,#7c5cfc)',
    ];
    return $gradients[abs(crc32($username)) % count($gradients)];
}

function roleBadgeClass(string $role): string {
    return match(strtolower($role)) {
        'admin', 'administrateur' => 'purple',
        'dev', 'developer'        => 'blue',
        'mod', 'moderator'        => 'green',
        default                   => 'muted',
    };
}

$today = (new DateTime())->format('l d F Y');
$daysMap   = ['Monday'=>'Lundi','Tuesday'=>'Mardi','Wednesday'=>'Mercredi','Thursday'=>'Jeudi','Friday'=>'Vendredi','Saturday'=>'Samedi','Sunday'=>'Dimanche'];
$monthsMap = ['January'=>'janvier','February'=>'février','March'=>'mars','April'=>'avril','May'=>'mai','June'=>'juin','July'=>'juillet','August'=>'août','September'=>'septembre','October'=>'octobre','November'=>'novembre','December'=>'décembre'];
foreach ($daysMap   as $en => $fr) $today = str_replace($en, $fr, $today);
foreach ($monthsMap as $en => $fr) $today = str_replace($en, $fr, $today);
?>

<!-- PAGE HEADER -->
<div class="page-header">
    <h1>Bonjour, <?= htmlspecialchars($_SESSION['username'] ?? '???') ?> 👋</h1>
    <p class="page-sub"><?= $today ?> — Voici un résumé de l'activité</p>
</div>
<!-- STAT CARDS -->
<div class="stats-grid">

    <div class="stat-card blue">
        <div class="stat-icon blue"><i class="ti ti-users" aria-hidden="true"></i></div>
        <div class="stat-val"><?= (int)($stats['users'] ?? 0) ?></div>
        <div class="stat-label">Utilisateurs actifs</div>
        <div class="stat-delta up">
            <i class="ti ti-trending-up" aria-hidden="true"></i>
            <?= (int)($stats['new_users_month'] ?? 0) ?> ce mois
        </div>
    </div>

    <div class="stat-card purple">
        <div class="stat-icon purple"><i class="ti ti-folder" aria-hidden="true"></i></div>
        <div class="stat-val"><?= (int)($stats['projects'] ?? 0) ?></div>
        <div class="stat-label">Projets en cours</div>
        <div class="stat-delta up">
            <i class="ti ti-trending-up" aria-hidden="true"></i>
            <?= (int)($stats['active_projects'] ?? 0) ?> actifs
        </div>
    </div>

    <div class="stat-card green">
        <div class="stat-icon green"><i class="ti ti-file-upload" aria-hidden="true"></i></div>
        <div class="stat-val"><?= (int)($stats['files'] ?? 0) ?></div>
        <div class="stat-label">Fichiers stockés</div>
        <div class="stat-delta up">
            <i class="ti ti-trending-up" aria-hidden="true"></i>
            <?= (int)($stats['folders'] ?? 0) ?> dossiers
        </div>
    </div>

    <div class="stat-card orange">
        <div class="stat-icon orange"><i class="ti ti-bug" aria-hidden="true"></i></div>
        <div class="stat-val"><?= (int)($stats['bugs'] ?? 0) ?></div>
        <div class="stat-label">Bugs signalés</div>
        <div class="stat-delta <?= ($stats['bugs'] ?? 0) > 0 ? 'down' : 'up' ?>">
            <i class="ti ti-<?= ($stats['bugs'] ?? 0) > 0 ? 'alert-circle' : 'check' ?>" aria-hidden="true"></i>
            <?= ($stats['bugs'] ?? 0) > 0 ? 'À traiter' : 'Aucun bug' ?>
        </div>
    </div>

</div>

<!-- ROW 2 : ACTIVITÉ + MEMBRES -->
<div class="row-2col">

    <div class="card">
        <div class="card-header">
            <span class="card-title">Activité récente</span>
            <a href="/logs/list" class="card-action">Voir les logs →</a>
        </div>
        <div class="list">
            <?php if (!empty($recent_logs)): ?>
            <?php foreach ($recent_logs as $log):
                    $dotColor = match($log['action'] ?? '') {
                        'create', 'add'    => 'green',
                        'update', 'edit'   => 'blue',
                        'delete', 'remove' => 'red',
                        'login',  'auth'   => 'purple',
                        default            => 'orange',
                    };
                ?>
            <div class="list-item">
                <span class="list-dot <?= $dotColor ?>"></span>
                <div class="list-item-body">
                    <div class="list-item-sub">
                        Action <strong><?= htmlspecialchars($log['action']) ?></strong>
                        <?php if (!empty($log['current'])): ?>
                        — <?= htmlspecialchars(mb_strimwidth($log['current'], 0, 60, '…')) ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="list-item-end">
                    <span class="list-item-time"><?= timeAgo((int)$log['activity_date']) ?></span>
                </div>
            </div>
            <?php endforeach; ?>
            <?php else: ?>
            <?php
                $demoLogs = [
                    ['dot'=>'blue',   'text'=>'<strong>kevin</strong> a rejoint le projet <strong>CrafterUniverse API</strong>', 'time'=>'il y a 8 min'],
                    ['dot'=>'green',  'text'=>'<strong>audrey</strong> a uploadé 3 fichiers dans <strong>/docs</strong>',        'time'=>'il y a 22 min'],
                    ['dot'=>'orange', 'text'=>'Nouveau bug signalé sur <strong>Dashboard v2</strong>',                           'time'=>'il y a 1h'],
                    ['dot'=>'purple', 'text'=>'<strong>wyatt</strong> a modifié les permissions de <strong>Dev Team</strong>',   'time'=>'il y a 2h'],
                ];
                foreach ($demoLogs as $l): ?>
            <div class="list-item">
                <span class="list-dot <?= $l['dot'] ?>"></span>
                <div class="list-item-body">
                    <div class="list-item-sub"><?= $l['text'] ?></div>
                </div>
                <div class="list-item-end">
                    <span class="list-item-time"><?= $l['time'] ?></span>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">Membres récents</span>
            <a href="/users/list" class="card-action">Gérer →</a>
        </div>
        <div class="list">
            <?php if (!empty($recent_users)): ?>
            <?php foreach ($recent_users as $u):
                    $initials = strtoupper(
                        substr($u['first_name'] ?? $u['username'], 0, 1) .
                        substr($u['last_name'] ?? '', 0, 1)
                    );
                    if (strlen($initials) < 2) $initials = strtoupper(substr($u['username'], 0, 2));
                ?>
            <div class="list-item">
                <div class="avatar" style="background:<?= getAvatarGradient($u['username']) ?>">
                    <?= htmlspecialchars($initials) ?>
                </div>
                <div class="list-item-body">
                    <div class="list-item-title"><?= htmlspecialchars($u['username']) ?></div>
                    <div class="list-item-sub"><?= htmlspecialchars($u['email'] ?? '') ?></div>
                </div>
                <div class="list-item-end">
                    <span class="badge <?= roleBadgeClass($u['role_name'] ?? '') ?>">
                        <?= htmlspecialchars($u['role_name'] ?? 'Membre') ?>
                    </span>
                </div>
            </div>
            <?php endforeach; ?>
            <?php else: ?>
            <?php
                $demoUsers = [
                    ['username'=>'kevin',  'email'=>'kevin@taderlafe.com',  'role'=>'Admin', 'badge'=>'purple'],
                    ['username'=>'audrey', 'email'=>'audrey@taderlafe.com', 'role'=>'Dev',   'badge'=>'blue'],
                    ['username'=>'eliza',  'email'=>'eliza@taderlafe.com',  'role'=>'Mod',   'badge'=>'green'],
                    ['username'=>'wyatt',  'email'=>'wyatt@taderlafe.com',  'role'=>'Admin', 'badge'=>'purple'],
                ];
                foreach ($demoUsers as $du): ?>
            <div class="list-item">
                <div class="avatar" style="background:<?= getAvatarGradient($du['username']) ?>">
                    <?= strtoupper(substr($du['username'], 0, 2)) ?>
                </div>
                <div class="list-item-body">
                    <div class="list-item-title"><?= $du['username'] ?></div>
                    <div class="list-item-sub"><?= $du['email'] ?></div>
                </div>
                <div class="list-item-end">
                    <span class="badge <?= $du['badge'] ?>"><?= $du['role'] ?></span>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</div>

<!-- ROW 3 : PROJETS + RESSOURCES -->
<div class="row-2col-equal">

    <div class="card">
        <div class="card-header">
            <span class="card-title">Projets actifs</span>
            <a href="/projects/list" class="card-action">Voir tout →</a>
        </div>
        <div class="list">
            <?php
            $projColors = ['blue', 'purple', 'orange', 'green', 'red'];
            if (!empty($recent_projects)):
                foreach ($recent_projects as $i => $proj):
                    $barColor = $projColors[$i % count($projColors)];
                    $status   = $proj['is_read_only'] ? 'muted' : 'green';
                    $label    = $proj['is_read_only'] ? 'Archivé' : 'Actif';
            ?>
            <div class="list-item">
                <span class="list-bar <?= $barColor ?>"></span>
                <div class="list-item-body">
                    <div class="list-item-title"><?= htmlspecialchars($proj['name']) ?></div>
                    <div class="list-item-sub">Créé le <?= date('d/m/Y', (int)$proj['created_at']) ?></div>
                </div>
                <div class="list-item-end">
                    <span class="badge <?= $status ?>"><?= $label ?></span>
                </div>
            </div>
            <?php endforeach; else: ?>
            <?php
                $demoProj = [
                    ['name'=>'CrafterUniverse API', 'meta'=>'8 membres · il y a 2h', 'badge'=>'green',  'label'=>'Actif',    'bar'=>'blue'],
                    ['name'=>'Dashboard v2',         'meta'=>'4 membres · il y a 5h', 'badge'=>'green',  'label'=>'Actif',    'bar'=>'purple'],
                    ['name'=>'Portail Clients',       'meta'=>'3 membres · hier',      'badge'=>'orange', 'label'=>'En pause', 'bar'=>'orange'],
                    ['name'=>'Migration BDD v3',      'meta'=>'2 membres · terminé',   'badge'=>'blue',   'label'=>'Terminé',  'bar'=>'green'],
                ];
                foreach ($demoProj as $p): ?>
            <div class="list-item">
                <span class="list-bar <?= $p['bar'] ?>"></span>
                <div class="list-item-body">
                    <div class="list-item-title"><?= $p['name'] ?></div>
                    <div class="list-item-sub"><?= $p['meta'] ?></div>
                </div>
                <div class="list-item-end">
                    <span class="badge <?= $p['badge'] ?>"><?= $p['label'] ?></span>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">Ressources système</span>
            <span class="card-action" id="js-refresh-sys">Actualiser</span>
        </div>

        <?php
        $sysCpu  = (int)($sys_stats['cpu']  ?? 0);
        $sysRam  = (int)($sys_stats['ram']  ?? 0);
        $sysDisk = (int)($sys_stats['disk'] ?? 0);
        $sysNet  = (int)($sys_stats['net']  ?? 0);
        ?>
        <div id="sys-bars">
            <div class="bar-row">
                <span class="bar-label">CPU</span>
                <div class="bar-track">
                    <div class="bar-fill" id="bar-cpu" style="width:<?= $sysCpu ?>%;background:var(--accent)"></div>
                </div>
                <span class="bar-val" id="val-cpu" style="color:var(--accent)"><?= $sysCpu ?>%</span>
            </div>
            <div class="bar-row">
                <span class="bar-label">RAM</span>
                <div class="bar-track">
                    <div class="bar-fill" id="bar-ram" style="width:<?= $sysRam ?>%;background:var(--accent2)"></div>
                </div>
                <span class="bar-val" id="val-ram" style="color:var(--accent2)"><?= $sysRam ?>%</span>
            </div>
            <div class="bar-row">
                <span class="bar-label">Disque</span>
                <div class="bar-track">
                    <div class="bar-fill" id="bar-disk" style="width:<?= $sysDisk ?>%;background:var(--success)"></div>
                </div>
                <span class="bar-val" id="val-disk" style="color:var(--success)"><?= $sysDisk ?>%</span>
            </div>
            <div class="bar-row">
                <span class="bar-label">Réseau</span>
                <div class="bar-track">
                    <div class="bar-fill" id="bar-net" style="width:<?= $sysNet ?>%;background:var(--warning)"></div>
                </div>
                <span class="bar-val" id="val-net" style="color:var(--warning)"><?= $sysNet ?>%</span>
            </div>
        </div>

        <p class="chart-title">Connexions (7 derniers jours)</p>
        <div class="chart-bars" id="js-mini-chart"
            data-values="<?= htmlspecialchars(json_encode($stats['daily_logins'] ?? [45,72,58,91,63,88,74])) ?>">
        </div>
    </div>

</div>

<script>
(function() {
    const chartEl = document.getElementById('js-mini-chart');
    const values = JSON.parse(chartEl.dataset.values || '[45,72,58,91,63,88,74]');
    const maxVal = Math.max(...values);
    values.forEach(function(v, i) {
        const bar = document.createElement('div');
        bar.className = 'chart-bar' + (i === values.length - 1 ? ' current' : '');
        bar.style.height = Math.round(v / maxVal * 100) + '%';
        bar.title = v + ' connexions';
        chartEl.appendChild(bar);
    });

    const bars = [{
            key: 'cpu',
            fill: 'bar-cpu',
            val: 'val-cpu'
        },
        {
            key: 'ram',
            fill: 'bar-ram',
            val: 'val-ram'
        },
        {
            key: 'disk',
            fill: 'bar-disk',
            val: 'val-disk'
        },
        {
            key: 'net',
            fill: 'bar-net',
            val: 'val-net'
        },
    ];

    function applyStats(data) {
        bars.forEach(function(b) {
            const pct = data[b.key] ?? 0;
            document.getElementById(b.fill).style.width = pct + '%';
            document.getElementById(b.val).textContent = pct + '%';
        });
    }

    document.getElementById('js-refresh-sys').addEventListener('click', function() {
        this.textContent = '…';
        const btn = this;
        fetch('/ajax/sysStats')
            .then(function(r) {
                return r.json();
            })
            .then(function(data) {
                applyStats(data);
                btn.textContent = 'Actualiser';
            })
            .catch(function() {
                btn.textContent = 'Actualiser';
            });
    });
})();
</script>