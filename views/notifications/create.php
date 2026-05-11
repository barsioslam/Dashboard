<!-- PAGE HEADER -->
<div class="page-header">
    <h1>Nouvelle notification</h1>
    <p class="page-sub">Créer et diffuser une notification</p>
    <a href="/notifications/list" class="btn btn-ghost">← Retour</a>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Informations</span>
    </div>
    <form method="POST" action="/notifications/create" class="form">
        <div class="form-group">
            <label for="message">Message</label>
            <textarea id="message" name="message" class="form-input" rows="3" required></textarea>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="type">Type</label>
                <select id="type" name="type" class="form-input">
                    <option value="info">Info</option>
                    <option value="warning">Avertissement</option>
                    <option value="danger">Danger</option>
                    <option value="success">Succès</option>
                </select>
            </div>
            <div class="form-group">
                <label for="start_date">Date de début</label>
                <input type="datetime-local" id="start_date" name="start_date" class="form-input">
            </div>
            <div class="form-group">
                <label for="end_date">Date de fin</label>
                <input type="datetime-local" id="end_date" name="end_date" class="form-input">
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Publier</button>
            <a href="/notifications/list" class="btn btn-ghost">Annuler</a>
        </div>
    </form>
</div>
