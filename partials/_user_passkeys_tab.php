<?php
    $userId = $formModel->id ?? null;
    $credentials = \IHORCHYSHKALA\Passkey\Models\WebauthnCredential::where('backend_user_id', $userId)
        ->orderBy('created_at', 'desc')
        ->get();
?>
<div class="passkey-manage-container" data-user-id="<?= e($userId) ?>">
    <div class="passkey-manage-header">
        <p class="passkey-description">
            <?= e(trans('ihorchyshkala.passkey::lang.manage.description')) ?>
        </p>
        <button
            type="button"
            id="passkey-register-btn"
            class="btn btn-primary"
        >
            <i class="icon-plus"></i>
            <?= e(trans('ihorchyshkala.passkey::lang.manage.add_button')) ?>
        </button>
    </div>

    <div id="passkey-register-form" class="passkey-register-form" style="display:none;">
        <div class="form-group">
            <label class="form-label"><?= e(trans('ihorchyshkala.passkey::lang.manage.name_label')) ?></label>
            <input type="text" id="passkey-name-input" class="form-control" placeholder="<?= e(trans('ihorchyshkala.passkey::lang.manage.name_placeholder')) ?>" maxlength="100" />
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

    <div id="passkey-manage-error" class="passkey-error" style="display:none;"></div>

    <div id="passkey-list-container">
        <?php include __DIR__ . '/_passkey_list.php'; /* uses $credentials, $userId */ ?>
    </div>
</div>
