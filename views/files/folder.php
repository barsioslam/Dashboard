<?php
$parentId  = $folder['from_folder'] ?? null;
$projectId = $folder['project_id']  ?? null;
$backUrl   = $parentId  ? '/files/folder/' . $parentId
           : ($projectId ? '/files/project/' . $projectId
           : '/files/list');
?>

<!-- PAGE HEADER -->
<div class="page-header-row">
    <div class="page-header">
        <!-- Breadcrumb -->
        <div style="display:flex;align-items:center;gap:6px;font-size:13px;flex-wrap:wrap;margin-bottom:4px;">
            <a href="/files/list" style="color:var(--muted);text-decoration:none;">Fichiers</a>
            <?php foreach ($breadcrumb as $i => $crumb): ?>
            <span style="color:var(--muted);">/</span>
            <?php if ($i < count($breadcrumb) - 1): ?>
            <a href="/files/folder/<?= (int) $crumb['id'] ?>" style="color:var(--muted);text-decoration:none;">
                <?= htmlspecialchars($crumb['name']) ?>
            </a>
            <?php else: ?>
            <span style="color:var(--text);font-weight:500;"><?= htmlspecialchars($crumb['name']) ?></span>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <h1>
            <?php if ($isPersonal): ?><i class="ti ti-lock" style="font-size:18px;margin-right:6px;"></i><?php endif; ?>
            <?= htmlspecialchars($folder['name']) ?>
        </h1>
    </div>
    <div class="page-actions">
        <button type="button" class="btn-save" onclick="document.getElementById('modal-upload').style.display='flex'">
            <i class="ti ti-upload"></i> Uploader
        </button>
        <button type="button" class="btn-secondary" onclick="document.getElementById('modal-subfolder').style.display='flex'">
            <i class="ti ti-folder-plus"></i> Sous-dossier
        </button>
        <?php if ($isOwner): ?>
        <button type="button" class="btn-secondary" onclick="document.getElementById('modal-rename-folder').style.display='flex'">
            <i class="ti ti-edit"></i>
        </button>
        <?php endif; ?>
        <a href="<?= $backUrl ?>" class="btn-secondary">
            <i class="ti ti-arrow-left"></i> Retour
        </a>
    </div>
</div>

<!-- Sous-dossiers -->
<?php if (!empty($subfolders)): ?>
<div class="card" style="margin-bottom:16px;">
    <div class="card-header">
        <span class="card-title">Sous-dossiers</span>
    </div>
    <div class="list">
        <?php foreach ($subfolders as $sub): ?>
        <div class="list-item">
            <div style="color:var(--primary,#4f8ef7);font-size:20px;margin-right:4px;">
                <i class="ti ti-folder"></i>
            </div>
            <div class="list-item-body">
                <a href="/files/folder/<?= (int) $sub['id'] ?>" style="font-weight:500;font-size:13px;color:var(--text);text-decoration:none;">
                    <?= htmlspecialchars($sub['name']) ?>
                </a>
                <div class="list-item-sub"><?= (int) $sub['file_count'] ?> fichier<?= $sub['file_count'] != 1 ? 's' : '' ?></div>
            </div>
            <div class="list-item-end">
                <a href="/files/folder/<?= (int) $sub['id'] ?>" class="table-btn">
                    <i class="ti ti-arrow-right"></i>
                </a>
                <form method="POST" action="/files/deleteFolder/<?= (int) $sub['id'] ?>" style="display:inline;"
                      onsubmit="return confirm('Supprimer ce dossier et tout son contenu ?')">
                    <button type="submit" class="table-btn danger"><i class="ti ti-trash"></i></button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Fichiers -->
<div class="card">
    <div class="card-header">
        <span class="card-title">Fichiers</span>
        <span style="font-size:12px;color:var(--muted);"><?= count($files) ?> fichier<?= count($files) != 1 ? 's' : '' ?></span>
    </div>
    <?php if (empty($files)): ?>
    <div class="table-empty">Aucun fichier dans ce dossier.</div>
    <?php else: ?>
    <div class="table-wrap">
        <div class="table">
            <div class="row header">
                <div class="cell">Nom</div>
                <div class="cell shrink">Taille</div>
                <div class="cell shrink">Partage</div>
                <div class="cell actions">Actions</div>
            </div>
            <?php foreach ($files as $file):
                $ext   = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $icon  = match(true) {
                    in_array($ext, ['jpg','jpeg','png','gif','webp','svg']) => 'ti-photo',
                    in_array($ext, ['pdf'])                                 => 'ti-file-type-pdf',
                    in_array($ext, ['zip','rar','7z','tar','gz'])           => 'ti-file-zip',
                    in_array($ext, ['doc','docx'])                          => 'ti-file-type-doc',
                    in_array($ext, ['xls','xlsx','csv'])                    => 'ti-file-type-xls',
                    in_array($ext, ['mp4','mov','avi','mkv'])               => 'ti-movie',
                    in_array($ext, ['mp3','wav','ogg'])                     => 'ti-music',
                    default                                                 => 'ti-file',
                };
                $filePath = UPLOAD_PATH . 'files/' . $file['real_name'];
                $bytes    = file_exists($filePath) ? filesize($filePath) : null;
                $size     = $bytes === null ? '—'
                          : ($bytes < 1024 ? $bytes . ' o'
                          : ($bytes < 1048576 ? round($bytes / 1024, 1) . ' Ko'
                          : round($bytes / 1048576, 1) . ' Mo'));
            ?>
            <div class="row">
                <div class="cell">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <i class="ti <?= $icon ?>" style="font-size:16px;color:var(--muted);"></i>
                        <span style="font-weight:500;font-size:13px;"><?= htmlspecialchars($file['name']) ?></span>
                    </div>
                </div>
                <div class="cell shrink muted"><?= htmlspecialchars($size) ?></div>
                <div class="cell shrink">
                    <?php if ($file['access_code']): ?>
                    <span class="badge green" style="cursor:pointer;"
                          title="Code : <?= htmlspecialchars($file['access_code']) ?>"
                          onclick="copyShareLink('<?= htmlspecialchars($file['access_code']) ?>')">
                        <i class="ti ti-link"></i> Partagé
                    </span>
                    <?php else: ?>
                    <span class="badge muted">Privé</span>
                    <?php endif; ?>
                </div>
                <div class="cell actions">
                    <a href="/files/download/<?= (int) $file['id'] ?>" class="table-btn" title="Télécharger">
                        <i class="ti ti-download"></i>
                    </a>
                    <form method="POST" action="/files/toggleShare/<?= (int) $file['id'] ?>" style="display:inline;">
                        <button type="submit" class="table-btn <?= $file['access_code'] ? 'orange' : '' ?>"
                                title="<?= $file['access_code'] ? 'Révoquer le partage' : 'Générer un lien de partage' ?>">
                            <i class="ti ti-<?= $file['access_code'] ? 'link-off' : 'share' ?>"></i>
                        </button>
                    </form>
                    <form method="POST" action="/files/deleteFile/<?= (int) $file['id'] ?>" style="display:inline;"
                          onsubmit="return confirm('Supprimer ce fichier ?')">
                        <button type="submit" class="table-btn danger"><i class="ti ti-trash"></i></button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Modal : upload -->
<div id="modal-upload" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);
     z-index:1000;align-items:center;justify-content:center;">
    <div class="card" style="width:440px;max-width:90vw;margin:0;">
        <div class="card-header">
            <span class="card-title">Uploader un fichier</span>
            <button type="button" onclick="document.getElementById('modal-upload').style.display='none'"
                    class="table-btn" style="margin-left:auto;"><i class="ti ti-x"></i></button>
        </div>
        <form method="POST" action="/files/upload/<?= (int) $folder['id'] ?>" enctype="multipart/form-data">
            <div class="field">
                <label for="upload_file">Fichier <span style="color:var(--danger)">*</span></label>
                <input type="file" id="upload_file" name="file" required>
            </div>
            <div class="field">
                <label for="display_name">Nom d'affichage <span style="color:var(--muted);font-weight:400;">(optionnel)</span></label>
                <input type="text" id="display_name" name="display_name" maxlength="100"
                       placeholder="Laissez vide pour garder le nom du fichier">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-save"><i class="ti ti-upload"></i> Uploader</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal : sous-dossier -->
<div id="modal-subfolder" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);
     z-index:1000;align-items:center;justify-content:center;">
    <div class="card" style="width:400px;max-width:90vw;margin:0;">
        <div class="card-header">
            <span class="card-title">Nouveau sous-dossier</span>
            <button type="button" onclick="document.getElementById('modal-subfolder').style.display='none'"
                    class="table-btn" style="margin-left:auto;"><i class="ti ti-x"></i></button>
        </div>
        <form method="POST" action="/files/createFolder">
            <input type="hidden" name="parent_id"  value="<?= (int) $folder['id'] ?>">
            <input type="hidden" name="project_id" value="<?= (int) ($folder['project_id'] ?? 0) ?: '' ?>">
            <div class="field">
                <label for="subfolder_name">Nom du dossier</label>
                <input type="text" id="subfolder_name" name="name" required maxlength="50" placeholder="Nom…">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-save"><i class="ti ti-folder-plus"></i> Créer</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal : renommer dossier -->
<?php if ($isOwner): ?>
<div id="modal-rename-folder" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);
     z-index:1000;align-items:center;justify-content:center;">
    <div class="card" style="width:400px;max-width:90vw;margin:0;">
        <div class="card-header">
            <span class="card-title">Renommer le dossier</span>
            <button type="button" onclick="document.getElementById('modal-rename-folder').style.display='none'"
                    class="table-btn" style="margin-left:auto;"><i class="ti ti-x"></i></button>
        </div>
        <form method="POST" action="/files/renameFolder/<?= (int) $folder['id'] ?>">
            <div class="field">
                <label for="rename_input">Nouveau nom</label>
                <input type="text" id="rename_input" name="name" required maxlength="50"
                       value="<?= htmlspecialchars($folder['name']) ?>">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-save"><i class="ti ti-check"></i> Renommer</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<script>
function copyShareLink(code) {
    const url = window.location.origin + '/files/shared/' + code;
    navigator.clipboard.writeText(url).then(() => {
        alert('Lien copié : ' + url);
    });
}
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.querySelectorAll('[id^="modal-"]').forEach(m => m.style.display = 'none');
    }
});
</script>
