<div class="auth-screen">
    <div class="auth-card">

        <div class="auth-brand">
            <span class="auth-brand-logo">TL</span>
            <span class="auth-brand-name">TaderLafe</span>
        </div>

        <h1 class="auth-title">Vérification 2FA</h1>
        <p class="auth-subtitle">Entrez le code à 6 chiffres généré par votre application d'authentification.</p>

        <?php if (!empty($messages['general'])): ?>
        <div class="msg-box danger">
            <?php foreach ($messages['general'] as $msg): ?>
            <p class="msg"><?= htmlspecialchars($msg) ?></p>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <form method="post" class="auth-form">
            <div class="form-group">
                <label>Code d'authentification</label>
                <input type="text" name="code" inputmode="numeric" pattern="\d{6}" maxlength="6" placeholder="000 000"
                    autocomplete="one-time-code" autofocus required
                    style="letter-spacing:.3em;font-size:22px;text-align:center;padding:12px">
            </div>
            <button type="submit" class="btn-login">
                <i class="ti ti-shield-check"></i> Vérifier
            </button>
        </form>

        <p style="text-align:center;margin-top:18px">
            <a href="/auth/login" style="font-size:12px;color:var(--muted);text-decoration:none">
                ← Retour à la connexion
            </a>
        </p>

    </div>
</div>