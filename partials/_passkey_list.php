<?php
    $userId = $userId ?? null;
?>
<?php if (isset($credentials) && $credentials->count() > 0): ?>
    <table class="table passkey-table">
        <thead>
            <tr>
                <th><?= e(trans('ihorchyshkala.passkey::lang.manage.col_name')) ?></th>
                <th><?= e(trans('ihorchyshkala.passkey::lang.manage.col_created')) ?></th>
                <th class="text-right"><?= e(trans('ihorchyshkala.passkey::lang.manage.col_actions')) ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($credentials as $credential): ?>
                <tr>
                    <td>
                        <i class="icon-key"></i>
                        <?= e($credential->name) ?>
                    </td>
                    <td><?= e($credential->created_at->format('Y-m-d H:i')) ?></td>
                    <td class="text-right">
                        <button
                            type="button"
                            class="btn btn-sm btn-danger"
                            data-request="onPasskeyDelete"
                            data-request-data="credential_id: <?= e($credential->id) ?>"
                            data-request-confirm="<?= e(trans('ihorchyshkala.passkey::lang.manage.confirm_delete')) ?>"
                        >
                            <i class="icon-trash"></i>
                            <?= e(trans('ihorchyshkala.passkey::lang.manage.delete')) ?>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="passkey-empty">
        <p><?= e(trans('ihorchyshkala.passkey::lang.manage.no_passkeys')) ?></p>
    </div>
<?php endif; ?>
