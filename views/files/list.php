<!-- PAGE HEADER -->
<div class="page-header-row">
    <div class="page-header">
        <h1>Mes fichiers</h1>
        <p class="page-sub">Dossiers personnels et partagés</p>
    </div>
    <div class="page-actions">
        <button type="button" class="btn-save" onclick="document.getElementById('modal-create-folder').style.display='flex'">
            <i class="ti ti-folder-plus"></i> Nouveau dossier
        </button>
    </div>
</div>

<!-- Mon espace personnel -->
<div class="card" style="margin-bottom:16px;">
    <div class="card-header">
        <span class="card-title"><i class="ti ti-lock" style="margin-right:6px;"></i>Mon espace personnel</span>
    </div>
    <?php if (empty($personal)): ?>
    <div class="table-empty">Aucun dossier personnel.</div>
    <?php else: ?>
    <div class="list">
        <?php foreach ($personal as $f): ?>
        <div class="list-item">
            <div class="list-dot" style="background:var(--primary,#4f8ef7);border-radius:50%;"></div>
            <div class="list-item-body">
                <a href="/files/folder/<?= (int) $f['id'] ?>" style="font-weight:500;font-size:13px;color:var(--text);text-decoration:none;">
                    <i class="ti ti-folder" style="margin-right:4px;"></i><?= htmlspecialchars($f['name']) ?>
                </a>
                <div class="list-item-sub"><?= (int) $f['file_count'] ?> fichier<?= $f['file_count'] != 1 ? 's' : '' ?></div>
            </div>
            <div class="list-item-end">
                <a href="/files/folder/<?= (int) $f['id'] ?>" class="table-btn">
                    <i class="ti ti-arrow-right"></i>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Dossiers partagés -->
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="ti ti-users" style="margin-right:6px;"></i>Dossiers partagés</span>
    </div>
    <?php if (empty($shared)): ?>
    <div class="table-empty">Aucun dossier partagé accessible.</div>
    <?php else: ?>
    <div class="list">
        <?php foreach ($shared as $f): ?>
        <div class="list-item">
            <div class="list-dot" style="background:#22c97a;border-radius:50%;"></div>
            <div class="list-item-body">
                <a href="/files/folder/<?= (int) $f['id'] ?>" style="font-weight:500;font-size:13px;color:var(--text);text-decoration:none;">
                    <i class="ti ti-folder" style="margin-right:4px;"></i><?= htmlspecialchars($f['name']) ?>
                </a>
                <div class="list-item-sub"><?= (int) $f['file_count'] ?> fichier<?= $f['file_count'] != 1 ? 's' : '' ?></div>
            </div>
            <div class="list-item-end">
                <a href="/files/folder/<?= (int) $f['id'] ?>" class="table-btn">
                    <i class="ti ti-arrow-right"></i>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Modal : nouveau dossier -->
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
            <input type="hidden" name="project_id" value="">
            <div class="field">
                <label for="folder_name">Nom du dossier</label>
                <input type="text" id="folder_name" name="name" required maxlength="50" placeholder="Nom…">
            </div>
            <div class="field" style="display:flex;align-items:center;gap:10px;padding:6px 0;">
                <input type="checkbox" id="is_personal" name="is_personal" value="1" style="width:16px;height:16px;">
                <label for="is_personal" style="margin:0;cursor:pointer;font-size:13px;">
                    <i class="ti ti-lock" style="margin-right:4px;"></i> Dossier personnel (visible par moi uniquement)
                </label>
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
