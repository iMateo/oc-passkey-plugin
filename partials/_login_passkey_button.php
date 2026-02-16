<div id="passkey-login-section">
    <div class="passkey-divider">
        <span><?= e(trans('ihorchyshkala.passkey::lang.login.divider')) ?></span>
    </div>
    <button
        type="button"
        id="passkey-login-btn"
        class="btn btn-secondary passkey-btn w-100"
    >
        <svg class="passkey-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M2 18v3c0 .6.4 1 1 1h4v-3h3v-3h2l1.4-1.4a6.5 6.5 0 1 0-4-4Z"/>
            <circle cx="16.5" cy="7.5" r=".5"/>
        </svg>
        <?= e(trans('ihorchyshkala.passkey::lang.login.button')) ?>
    </button>
    <div id="passkey-login-error" class="passkey-error" style="display:none;"></div>
</div>
