<!-- PAGE HEADER -->
<div class="page-header">
    <h1>Modifier notification</h1>
    <p class="page-sub">Mise à jour de la notification</p>
    <a href="/notifications/list" class="btn btn-ghost">← Retour</a>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Informations</span>
    </div>
    <form method="POST" action="/notifications/edit/<?= (int)($notification['id'] ?? 0) ?>" class="form">
        <div class="form-group">
            <label for="message">Message</label>
            <textarea id="message" name="message" class="form-input" rows="3" required><?= htmlspecialchars($notification['message'] ?? '') ?></textarea>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="type">Type</label>
                <select id="type" name="type" class="form-input">
                    <?php foreach (['info', 'warning', 'danger', 'success'] as $t): ?>
                    <option value="<?= $t ?>" <?= ($notification['type'] ?? '') === $t ? 'selected' : '' ?>>
                        <?= ucfirst($t) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="start_date">Date de début</label>
                <input type="datetime-local" id="start_date" name="start_date" class="form-input"
                       value="<?= !empty($notification['start_date']) ? date('Y-m-d\TH:i', (int)$notification['start_date']) : '' ?>">
            </div>
            <div class="form-group">
                <label for="end_date">Date de fin</label>
                <input type="datetime-local" id="end_date" name="end_date" class="form-input"
                       value="<?= !empty($notification['end_date']) ? date('Y-m-d\TH:i', (int)$notification['end_date']) : '' ?>">
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="/notifications/list" class="btn btn-ghost">Annuler</a>
        </div>
    </form>
</div>
