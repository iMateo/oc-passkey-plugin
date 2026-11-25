<?php
    $userId = $formModel->id ?? null;
    $currentBackendUser = \BackendAuth::getUser();
    // Registration is offered only on the signed-in user's own account (enforced server-side too)
    $canRegister = $currentBackendUser
        && $userId !== null
        && (string) $currentBackendUser->id === (string) $userId
        && !\BackendAuth::isImpersonator();
    $credentials = \IHORCHYSHKALA\Passkey\Models\WebauthnCredential::where('backend_user_id', $userId)
        ->orderBy('created_at', 'desc')
        ->get();
?>
<div class="passkey-manage-container" data-user-id="<?= e($userId) ?>">
    <div class="passkey-manage-header">
        <p class="passkey-description">
            <?= e(trans('ihorchyshkala.passkey::lang.manage.description')) ?>
        </p>
        <?php if (!$canRegister): ?>
            <p class="passkey-description">
                <?= e(trans('ihorchyshkala.passkey::lang.manage.owner_only')) ?>
            </p>
        <?php else: ?>
        <button
            type="button"
            id="passkey-register-btn"
            class="btn btn-primary"
        >
            <i class="icon-plus"></i>
            <?= e(trans('ihorchyshkala.passkey::lang.manage.add_button')) ?>
        </button>
        <?php endif; ?>
    </div>

    <?php if ($canRegister): ?>
    <div id="passkey-register-form" class="passkey-register-form" style="display:none;">
        <div class="form-group">
            <label class="form-label"><?= e(trans('ihorchyshkala.passkey::lang.manage.name_label')) ?></label>
            <input type="text" id="passkey-name-input" class="form-control" placeholder="<?= e(trans('ihorchyshkala.passkey::lang.manage.name_placeholder')) ?>" maxlength="100" />
        </div>
        <div class="form-group">
            <label class="form-label" for="passkey-password-input"><?= e(trans('ihorchyshkala.passkey::lang.manage.password_label')) ?></label>
            <input type="password" id="passkey-password-input" class="form-control" autocomplete="current-password" />
        </div>
        <div class="passkey-register-actions">
            <button type="button" id="passkey-register-confirm" class="btn btn-success">
                <i class="icon-key"></i>
                <?= e(trans('ihorchyshkala.passkey::lang.manage.register_button')) ?>
            </button>
            <button type="button" id="passkey-register-cancel" class="btn btn-secondary">
                <?= e(trans('ihorchyshkala.passkey::lang.manage.cancel')) ?>
            </button>
        </div>
    </div>
    <?php endif; ?>

    <div id="passkey-manage-error" class="passkey-error" style="display:none;"></div>

    <div id="passkey-list-container">
        <?php include __DIR__ . '/_passkey_list.php'; /* uses $credentials, $userId */ ?>
    </div>
</div>
