<?php
// Pré-remplissage : priorité BDD ($currentUser injecté par le controller)
$username  = htmlspecialchars($currentUser['username']   ?? $_SESSION['username']   ?? '');
$email     = htmlspecialchars($currentUser['email']      ?? $_SESSION['email']      ?? '');
$firstName = htmlspecialchars($currentUser['first_name'] ?? $_SESSION['first_name'] ?? '');
$lastName  = htmlspecialchars($currentUser['last_name']  ?? $_SESSION['last_name']  ?? '');
$tab            = $_GET['tab'] ?? 'account';
$messages       = $messages       ?? [];
$tfaEnabled     = $tfaEnabled     ?? false;
$tfaSetupSecret = $tfaSetupSecret ?? null;
$tfaSetupUri    = $tfaSetupUri    ?? null;
$activeSessions = $activeSessions ?? [];
$currentToken   = $currentToken   ?? '';
?>

<!-- PAGE HEADER -->
<div class="page-header">
    <h1>Paramètres</h1>
    <p class="page-sub">Configuration du panneau d'administration</p>
</div>

<div class="settings-wrap">

    <!-- NAV LATÉRALE -->
    <nav class="settings-nav">
        <a href="?tab=account" class="settings-nav-item <?= $tab === 'account'       ? 'active' : '' ?>">
            <i class="ti ti-user-circle"></i> Compte
        </a>
        <a href="?tab=security" class="settings-nav-item <?= $tab === 'security'      ? 'active' : '' ?>">
            <i class="ti ti-shield-lock"></i> Sécurité
        </a>
        <a href="?tab=notifications" class="settings-nav-item <?= $tab === 'notifications' ? 'active' : '' ?>">
            <i class="ti ti-bell"></i> Notifications
        </a>
        <div class="settings-nav-sep"></div>
        <a href="?tab=danger" class="settings-nav-item <?= $tab === 'danger'        ? 'active' : '' ?>"
            style="color:var(--danger)">
            <i class="ti ti-alert-triangle"></i> Danger
        </a>
    </nav>

    <!-- CONTENU -->
    <div>

        <?php if ($tab === 'account'): ?>
        <!-- ===== COMPTE ===== -->
        <div class="card">
            <div class="settings-section-title">Informations personnelles</div>

            <?php if (!empty($messages['success'])): ?>
            <div class="alert success">
                <i class="ti ti-circle-check"></i>
                <?= htmlspecialchars($messages['success'][0]) ?>
            </div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="_section" value="account">
                <div class="field-row">
                    <div class="field">
                        <label>Prénom</label>
                        <input type="text" name="first_name" value="<?= $firstName ?>" placeholder="Prénom">
                    </div>
                    <div class="field">
                        <label>Nom de famille</label>
                        <input type="text" name="last_name" value="<?= $lastName ?>" placeholder="Nom">
                    </div>
                </div>
                <div class="field-row">
                    <div class="field <?= !empty($messages['username']) ? 'has-error' : '' ?>">
                        <label>Nom d'utilisateur</label>
                        <input type="text" name="username" value="<?= $username ?>" placeholder="username">
                        <?php if (!empty($messages['username'])): ?>
                        <span class="field-error"><?= htmlspecialchars($messages['username'][0]) ?></span>
                        <?php else: ?>
                        <span class="field-hint">3 à 20 caractères, lettres et chiffres uniquement</span>
                        <?php endif; ?>
                    </div>
                    <div class="field <?= !empty($messages['email']) ? 'has-error' : '' ?>">
                        <label>Adresse e-mail</label>
                        <input type="email" name="email" value="<?= $email ?>" placeholder="email@exemple.com">
                        <?php if (!empty($messages['email'])): ?>
                        <span class="field-error"><?= htmlspecialchars($messages['email'][0]) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-save">
                        <i class="ti ti-device-floppy"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>

        <?php elseif ($tab === 'security'): ?>
        <!-- ===== SÉCURITÉ ===== -->

        <!-- 2FA -->
        <div class="card">
            <div class="settings-section-title">Authentification à deux facteurs (2FA)</div>

            <?php if ($tfaEnabled): ?>
            <?php if (!empty($messages['2fa_success'])): ?>
            <div class="alert success"><i class="ti ti-circle-check"></i> 2FA activé avec succès.</div>
            <?php endif; ?>
            <?php if (!empty($messages['2fa_disabled'])): ?>
            <div class="alert success"><i class="ti ti-circle-check"></i> 2FA désactivé.</div>
            <?php endif; ?>
            <div class="tfa-status">
                <span class="tfa-badge enabled"><i class="ti ti-shield-check"></i> Activé</span>
                <p>L'authentification à deux facteurs est active. Un code Google Authenticator sera demandé à chaque
                    connexion.</p>
            </div>
            <form method="POST" style="margin-top:16px">
                <input type="hidden" name="_section" value="2fa_disable">
                <button type="submit" class="btn-danger">
                    <i class="ti ti-shield-x"></i> Désactiver le 2FA
                </button>
            </form>

            <?php elseif ($tfaSetupSecret): ?>
            <p class="tfa-hint">Scannez ce QR code avec Google Authenticator, puis entrez le code à 6 chiffres pour
                confirmer.</p>
            <div class="tfa-setup">
                <div id="js-qr"></div>
                <div class="tfa-key">
                    <span class="tfa-key-label">Clé manuelle</span>
                    <code class="tfa-key-value"><?= htmlspecialchars($tfaSetupSecret) ?></code>
                </div>
            </div>
            <?php if (!empty($messages['2fa_error'])): ?>
            <div class="alert error"><i class="ti ti-alert-circle"></i> Code invalide. Veuillez réessayer.</div>
            <?php endif; ?>
            <!-- FORM CONFIRM -->
            <form method="POST">
                <input type="hidden" name="_section" value="2fa_confirm">

                <div class="field">
                    <label>Code de vérification</label>
                    <input type="text" name="code" inputmode="numeric" pattern="\d{6}" maxlength="6"
                        placeholder="000000" autocomplete="one-time-code" autofocus required
                        style="letter-spacing:.25em;font-size:18px;text-align:center">

                    <span class="field-hint">
                        Code à 6 chiffres affiché dans l'application
                    </span>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-save">
                        <i class="ti ti-check"></i> Confirmer
                    </button>
                </div>
            </form>

            <!-- FORM CANCEL -->
            <form method="POST">
                <input type="hidden" name="_section" value="2fa_cancel">

                <div class="form-actions" style="margin-top:12px;padding-top:0;border:none">
                    <button type="submit" class="btn-secondary">
                        Annuler
                    </button>
                </div>
            </form>
            <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
            <script>
            new QRCode(document.getElementById('js-qr'), {
                text: <?= json_encode($tfaSetupUri) ?>,
                width: 172,
                height: 172,
                colorDark: '#000000',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.M
            });
            </script>

            <?php else: ?>
            <div class="tfa-status">
                <span class="tfa-badge"><i class="ti ti-shield-off"></i> Désactivé</span>
                <p>Sécurisez votre compte avec Google Authenticator. Un code temporaire sera requis à chaque connexion.
                </p>
            </div>
            <form method="POST" style="margin-top:16px">
                <input type="hidden" name="_section" value="2fa_init">
                <button type="submit" class="btn-save">
                    <i class="ti ti-shield-plus"></i> Configurer le 2FA
                </button>
            </form>
            <?php endif; ?>
        </div>

        <!-- Mot de passe -->
        <div class="card" style="margin-top:16px">
            <div class="settings-section-title">Changer le mot de passe</div>
            <form method="POST">
                <input type="hidden" name="_section" value="password">
                <div class="field">
                    <label>Mot de passe actuel</label>
                    <input type="password" name="current_password" placeholder="••••••••••••">
                </div>
                <div class="field-row">
                    <div class="field">
                        <label>Nouveau mot de passe</label>
                        <input type="password" name="new_password" placeholder="••••••••••••">
                        <span class="field-hint">Minimum 12 caractères</span>
                    </div>
                    <div class="field">
                        <label>Confirmer</label>
                        <input type="password" name="confirm_password" placeholder="••••••••••••">
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-save">
                        <i class="ti ti-key"></i> Mettre à jour
                    </button>
                </div>
            </form>
        </div>

        <!-- Sessions actives -->
        <div class="card" style="margin-top:16px">
            <div class="settings-section-title">Sessions actives</div>

            <?php if (!empty($messages['session_revoked'])): ?>
            <div class="alert success"><i class="ti ti-circle-check"></i> Session révoquée.</div>
            <?php endif; ?>
            <?php if (!empty($messages['sessions_revoked'])): ?>
            <div class="alert success"><i class="ti ti-circle-check"></i> Toutes les autres sessions ont été révoquées.</div>
            <?php endif; ?>

            <?php
            $deviceIcon = static function(string $device): string {
                return match ($device) {
                    'Mobile'   => 'ti-device-mobile',
                    'Tablette' => 'ti-device-tablet',
                    default    => 'ti-device-laptop',
                };
            };

            $activityStatus = static function(int $ts): string {
                $diff = time() - $ts;
                if ($diff < 300)   return 'online';
                if ($diff < 3600)  return 'recent';
                return 'idle';
            };

            if (!function_exists('sessionAge')) {
                function sessionAge(int $ts): string {
                    $diff = time() - $ts;
                    if ($diff < 60)    return 'à l\'instant';
                    if ($diff < 3600)  return 'il y a ' . floor($diff / 60) . ' min';
                    if ($diff < 86400) return 'il y a ' . floor($diff / 3600) . 'h';
                    return 'il y a ' . floor($diff / 86400) . 'j';
                }
            }

            $otherCount = count(array_filter($activeSessions, fn($s) => $s['token'] !== $currentToken));
            ?>

            <?php if (empty($activeSessions)): ?>
            <p class="session-empty">Aucune session enregistrée.</p>
            <?php else: ?>

            <div class="session-list">
                <?php foreach ($activeSessions as $s):
                    $isCurrent = $s['token'] === $currentToken;
                    $status    = $activityStatus((int)$s['last_activity']);
                ?>
                <div class="session-item <?= $isCurrent ? 'is-current' : '' ?>">

                    <!-- Icône appareil -->
                    <div class="session-device">
                        <i class="ti <?= $deviceIcon($s['device']) ?>"></i>
                        <span class="session-status-dot <?= $status ?>"></span>
                    </div>

                    <!-- Infos principales -->
                    <div class="session-body">
                        <div class="session-title">
                            <span><?= htmlspecialchars($s['browser']) ?></span>
                            <span class="session-os">sur <?= htmlspecialchars($s['os']) ?></span>
                            <?php if ($isCurrent): ?>
                            <span class="session-badge">Actuelle</span>
                            <?php endif; ?>
                        </div>
                        <div class="session-sub">
                            <i class="ti ti-map-pin"></i>
                            <span><?= htmlspecialchars($s['ip']) ?></span>
                            <?php if (!empty($s['country'])): ?>
                            <span class="session-dot"></span>
                            <span><?= htmlspecialchars($s['country']) ?></span>
                            <?php endif; ?>
                            <span class="session-dot"></span>
                            <i class="ti ti-clock"></i>
                            <span><?= sessionAge((int)$s['last_activity']) ?></span>
                            <span class="session-dot"></span>
                            <span>connecté le <?= date('d/m/Y à H:i', (int)$s['created_at']) ?></span>
                        </div>
                    </div>

                    <!-- Action -->
                    <div class="session-action">
                        <?php if ($isCurrent): ?>
                        <span class="session-current-label"><i class="ti ti-check"></i></span>
                        <?php else: ?>
                        <form method="POST">
                            <input type="hidden" name="_section" value="session_revoke">
                            <input type="hidden" name="token" value="<?= htmlspecialchars($s['token']) ?>">
                            <button type="submit" class="session-revoke-btn" title="Révoquer cette session">
                                <i class="ti ti-x"></i>
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>

                </div>
                <?php endforeach; ?>
            </div>

            <?php if ($otherCount > 0): ?>
            <div class="session-footer">
                <span class="session-footer-info">
                    <?= $otherCount ?> autre<?= $otherCount > 1 ? 's' : '' ?> session<?= $otherCount > 1 ? 's' : '' ?> active<?= $otherCount > 1 ? 's' : '' ?>
                </span>
                <form method="POST">
                    <input type="hidden" name="_section" value="session_revoke_all">
                    <button type="submit" class="btn-danger">
                        <i class="ti ti-shield-x"></i> Tout révoquer
                    </button>
                </form>
            </div>
            <?php endif; ?>

            <?php endif; ?>
        </div>

        <?php elseif ($tab === 'notifications'): ?>
        <!-- ===== NOTIFICATIONS ===== -->
        <div class="card">
            <div class="settings-section-title">Notifications e-mail</div>
            <form method="POST">
                <input type="hidden" name="_section" value="notifications">
                <?php
                $notifs = [
                    ['key' => 'notif_new_user',   'label' => 'Nouvel utilisateur',     'sub' => 'Recevoir un e-mail à chaque nouvelle inscription',    'on' => true],
                    ['key' => 'notif_new_bug',    'label' => 'Bug signalé',            'sub' => 'Être alerté lors d\'un nouveau rapport de bug',        'on' => true],
                    ['key' => 'notif_new_msg',    'label' => 'Nouveau message',        'sub' => 'Notification à chaque message reçu',                   'on' => false],
                    ['key' => 'notif_login_fail', 'label' => 'Tentative de connexion', 'sub' => 'Alerte en cas d\'échec de connexion répété',           'on' => true],
                    ['key' => 'notif_system',     'label' => 'Alertes système',        'sub' => 'Rapport hebdomadaire d\'utilisation des ressources',   'on' => false],
                ];
                foreach ($notifs as $n): ?>
                <div class="toggle-row">
                    <div class="toggle-info">
                        <div class="toggle-label"><?= htmlspecialchars($n['label']) ?></div>
                        <div class="toggle-sub"><?= htmlspecialchars($n['sub']) ?></div>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="<?= $n['key'] ?>" <?= $n['on'] ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                </div>
                <?php endforeach; ?>
                <div class="form-actions">
                    <button type="submit" class="btn-save">
                        <i class="ti ti-device-floppy"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>

        <?php elseif ($tab === 'danger'): ?>
        <!-- ===== DANGER ===== -->
        <div class="card danger-card">
            <div class="settings-section-title">Zone de danger</div>
            <div class="toggle-row">
                <div class="toggle-info">
                    <div class="toggle-label">Mode maintenance</div>
                    <div class="toggle-sub">Rend le site inaccessible aux utilisateurs non-admins</div>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" name="maintenance_mode">
                    <span class="slider"></span>
                </label>
            </div>
            <div class="toggle-row">
                <div class="toggle-info">
                    <div class="toggle-label">Inscriptions ouvertes</div>
                    <div class="toggle-sub">Autorise les nouvelles inscriptions publiques</div>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" name="allow_register" checked>
                    <span class="slider"></span>
                </label>
            </div>
            <div class="toggle-row">
                <div class="toggle-info">
                    <div class="toggle-label">Vider le cache</div>
                    <div class="toggle-sub">Supprime tous les fichiers de cache temporaires</div>
                </div>
                <button class="btn-secondary" type="button">
                    <i class="ti ti-trash"></i> Vider
                </button>
            </div>
            <div class="toggle-row">
                <div class="toggle-info">
                    <div class="toggle-label" style="color:var(--danger)">Réinitialiser la base de données</div>
                    <div class="toggle-sub">Supprime toutes les données utilisateurs. Action irréversible.</div>
                </div>
                <button class="btn-danger" type="button">
                    <i class="ti ti-database-x"></i> Réinitialiser
                </button>
            </div>
        </div>

        <?php endif; ?>

    </div>

</div>