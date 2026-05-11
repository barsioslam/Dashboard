<?php use App\Utils\Text\Text; ?>

<div class="auth-screen">
    <div class="auth-card">

        <div class="auth-brand">
            <span class="auth-brand-logo">TL</span>
            <span class="auth-brand-name">TaderLafe</span>
        </div>

        <h1 class="auth-title"><?= Text::write('$lang::auth/login/title'); ?></h1>
        <p class="auth-subtitle"><?= Text::write('$lang::auth/login/subtitle'); ?></p>

        <?php if (!empty($messages['general'])) { ?>
        <div class="msg-box danger">
            <?php foreach ($messages['general'] as $message) { ?>
            <p class="msg"><?= Text::write($message); ?></p>
            <?php } ?>
        </div>
        <?php } ?>

        <form method="post" class="auth-form">

            <div class="form-group <?= !empty($messages['username']) ? 'has-error' : ''; ?>">
                <label for="usernameInput"><?= Text::write('$lang::forms/username'); ?></label>
                <input type="text" name="username" id="usernameInput"
                       placeholder="<?= Text::write('$lang::forms/username_placeholder'); ?>"
                       value="<?= htmlspecialchars($_POST['username'] ?? ''); ?>"
                       autocomplete="username" required>
                <?php if (!empty($messages['username'])) { ?>
                <div class="field-error">
                    <?php foreach ($messages['username'] as $message) { ?>
                    <span><?= Text::write($message); ?></span>
                    <?php } ?>
                </div>
                <?php } ?>
            </div>

            <div class="form-group <?= !empty($messages['password']) ? 'has-error' : ''; ?>">
                <label for="passInput"><?= Text::write('$lang::forms/password'); ?></label>
                <div class="input-password">
                    <input type="password" name="password" id="passInput"
                           placeholder="••••••••••••"
                           autocomplete="current-password" required>
                    <button type="button" class="toggle-pass" aria-label="Afficher le mot de passe" onclick="togglePassword(this)">
                        <svg data-eye="show" xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg data-eye="hide" xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                    </button>
                </div>
                <?php if (!empty($messages['password'])) { ?>
                <div class="field-error">
                    <?php foreach ($messages['password'] as $message) { ?>
                    <span><?= Text::write($message); ?></span>
                    <?php } ?>
                </div>
                <?php } ?>
            </div>

            <button type="submit" name="submit" class="btn-login">
                <?= Text::write('$lang::general/login'); ?>
            </button>

        </form>
    </div>
</div>

<script>
function togglePassword(btn) {
    const input = document.getElementById('passInput');
    const show  = btn.querySelector('[data-eye="show"]');
    const hide  = btn.querySelector('[data-eye="hide"]');
    if (input.type === 'password') {
        input.type      = 'text';
        show.style.display = 'none';
        hide.style.display = 'block';
    } else {
        input.type      = 'password';
        show.style.display = 'block';
        hide.style.display = 'none';
    }
}
</script>