<!-- PAGE HEADER -->
<div class="page-header-row">
    <div class="page-header">
        <h1><i class="ti ti-folders" style="margin-right:8px;"></i>Fichiers — <?= htmlspecialchars($project['name']) ?></h1>
        <p class="page-sub">Dossiers rattachés au projet</p>
    </div>
    <div class="page-actions">
        <button type="button" class="btn-save" onclick="document.getElementById('modal-create-folder').style.display='flex'">
            <i class="ti ti-folder-plus"></i> Nouveau dossier
        </button>
        <a href="/projects/view/<?= (int) $project['id'] ?>" class="btn-secondary">
            <i class="ti ti-arrow-left"></i> Fiche projet
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Dossiers du projet</span>
        <span style="font-size:12px;color:var(--muted);"><?= count($folders) ?> dossier<?= count($folders) != 1 ? 's' : '' ?></span>
    </div>
    <?php if (empty($folders)): ?>
    <div class="table-empty">Aucun dossier pour ce projet.</div>
    <?php else: ?>
    <div class="list">
        <?php foreach ($folders as $f): ?>
        <div class="list-item">
            <div style="color:var(--primary,#4f8ef7);font-size:20px;margin-right:4px;">
                <i class="ti ti-folder"></i>
            </div>
            <div class="list-item-body">
                <a href="/files/folder/<?= (int) $f['id'] ?>" style="font-weight:500;font-size:13px;color:var(--text);text-decoration:none;">
                    <?= htmlspecialchars($f['name']) ?>
                </a>
                <div class="list-item-sub"><?= (int) $f['file_count'] ?> fichier<?= $f['file_count'] != 1 ? 's' : '' ?></div>
            </div>
            <div class="list-item-end">
                <a href="/files/folder/<?= (int) $f['id'] ?>" class="table-btn">
                    <i class="ti ti-arrow-right"></i>
                </a>
                <form method="POST" action="/files/deleteFolder/<?= (int) $f['id'] ?>" style="display:inline;"
                      onsubmit="return confirm('Supprimer ce dossier et tout son contenu ?')">
                    <button type="submit" class="table-btn danger"><i class="ti ti-trash"></i></button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Modal : nouveau dossier projet -->
<div id="modal-create-folder" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);
     z-index:1000;align-items:center;justify-content:center;">
    <div class="card" style="width:400px;max-width:90vw;margin:0;">
        <div class="card-header">
            <span class="card-title">Nouveau dossier</span>
            <button type="button" onclick="document.getElementById('modal-create-folder').style.display='none'"
                    class="table-btn" style="margin-left:auto;"><i class="ti ti-x"></i></button>
        </div>
        <form method="POST" action="/files/createFolder">
            <input type="hidden" name="parent_id"  value="">
            <input type="hidden" name="project_id" value="<?= (int) $projectId ?>">
            <div class="field">
                <label for="folder_name">Nom du dossier</label>
                <input type="text" id="folder_name" name="name" required maxlength="50" placeholder="Nom…">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-save"><i class="ti ti-folder-plus"></i> Créer</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') document.getElementById('modal-create-folder').style.display = 'none';
});
</script>
