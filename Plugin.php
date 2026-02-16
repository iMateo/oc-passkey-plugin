<?php

namespace IHORCHYSHKALA\Passkey;

use Log;
use Event;
use Backend;
use BackendAuth;
use Backend\Models\User as BackendUserModel;
use Backend\Models\AccessLog;
use Backend\Controllers\Auth as AuthController;
use Backend\Controllers\Users as UsersController;
use IHORCHYSHKALA\Passkey\Models\WebauthnCredential;
use IHORCHYSHKALA\Passkey\Services\WebauthnService;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public $elevated = true;

    public function pluginDetails()
    {
        return [
            'name'        => 'ihorchyshkala.passkey::lang.plugin.name',
            'description' => 'ihorchyshkala.passkey::lang.plugin.description',
            'author'      => 'IC Studio',
            'icon'        => 'icon-key',
        ];
    }

    public function boot()
    {
        $this->extendBackendUserModel();
        $this->extendAuthController();
        $this->extendUsersController();
        $this->injectLoginAssets();
    }

    /**
     * Add hasMany relation to Backend\Models\User
     */
    protected function extendBackendUserModel()
    {
        BackendUserModel::extend(function ($model) {
            $model->hasMany['webauthn_credentials'] = [
                WebauthnCredential::class,
                'key' => 'backend_user_id',
            ];
        });
    }

    /**
     * Inject passkey button into login page via view event
     */
    protected function injectLoginAssets()
    {
        // Inject passkey button below the standard login form
        Event::listen('backend.auth.extendSigninView', function ($controller) {
            return $controller->makePartial(
                '$/ihorchyshkala/passkey/partials/_login_passkey_button'
            );
        });

        // Inject JS/CSS assets on auth layout
        Event::listen('backend.layout.extendHead', function ($controller, $layout = null) {
            $pluginPath = '/plugins/ihorchyshkala/passkey/assets';
            $html = '';
            $html .= '<link href="' . url($pluginPath . '/css/passkey.css') . '" rel="stylesheet" />' . PHP_EOL;

            // Only load login JS on the auth layout
            if ($layout === 'auth' || (isset($controller->bodyClass) && $controller->bodyClass === 'signin')) {
                $html .= '<script src="' . url($pluginPath . '/js/passkey-login.js') . '" defer></script>' . PHP_EOL;
            }

            return $html;
        });
    }

    /**
     * Add AJAX handlers to Auth controller for passkey login
     */
    protected function extendAuthController()
    {
        AuthController::extend(function ($controller) {
            $controller->addDynamicMethod('onPasskeyLoginOptions', function () {
                $service = new WebauthnService();
                $options = $service->createLoginOptions();

                return ['options' => $options];
            });

            $controller->addDynamicMethod('onPasskeyLoginVerify', function () {
                $ip = request()->ip();

                // Rate limiting: max 10 passkey attempts per minute per IP
                $cacheKey = 'passkey_login_attempts_' . md5($ip);
                $attempts = \Cache::get($cacheKey, 0);
                if ($attempts >= 10) {
                    Log::warning('Passkey login rate limited', ['ip' => $ip]);
                    throw new \ApplicationException('Too many attempts. Please wait a moment.');
                }
                \Cache::put($cacheKey, $attempts + 1, 60);

                try {
                    $service = new WebauthnService();

                    $user = $service->verifyLogin(
                        post('credential_id'),
                        post('client_data_json'),
                        post('authenticator_data'),
                        post('signature'),
                        post('user_handle')
                    );
                } catch (\Exception $e) {
                    Log::warning('Passkey login failed', [
                        'ip' => $ip,
                        'error' => $e->getMessage(),
                    ]);
                    throw $e;
                }

                // Clear rate limit on success
                \Cache::forget($cacheKey);

                // Log the user in (passwordless)
                BackendAuth::login($user, true);

                // Log access
                AccessLog::add($user);

                Log::info('Passkey login successful', [
                    'user_id' => $user->id,
                    'ip' => $ip,
                ]);

                return [
                    'redirect' => Backend::url('backend'),
                ];
            });
        });
    }

    /**
     * Add Passkeys tab and AJAX handlers to Users controller
     */
    protected function extendUsersController()
    {
        // Add form tab
        UsersController::extendFormFields(function ($form, $model, $context) {
            if (!$model instanceof BackendUserModel) {
                return;
            }
            if (!$model->exists) {
                return;
            }

            $form->addTabFields([
                'passkeys_partial' => [
                    'type'    => 'partial',
                    'path'    => '$/ihorchyshkala/passkey/partials/_user_passkeys_tab',
                    'tab'     => 'ihorchyshkala.passkey::lang.tab.passkeys',
                    'context' => ['update', 'myaccount'],
                ],
            ]);
        });

        // Add AJAX handlers and assets
        $plugin = $this;
        UsersController::extend(function ($controller) use ($plugin) {
            $controller->addCss('/plugins/ihorchyshkala/passkey/assets/css/passkey.css');
            $controller->addJs('/plugins/ihorchyshkala/passkey/assets/js/passkey-manage.js');

            // Start registration ceremony
            $controller->addDynamicMethod('onPasskeyRegisterOptions', function () use ($controller, $plugin) {
                $user = $plugin->getTargetUser($controller);
                $service = new WebauthnService();
                $options = $service->createRegistrationOptions($user);

                return ['options' => $options];
            });

            // Complete registration ceremony
            $controller->addDynamicMethod('onPasskeyRegisterVerify', function () use ($controller, $plugin) {
                $user = $plugin->getTargetUser($controller);
                $service = new WebauthnService();

                $credential = $service->verifyRegistration(
                    $user,
                    post('client_data_json'),
                    post('attestation_object'),
                    post('passkey_name', 'Passkey')
                );

                \Flash::success(trans('ihorchyshkala.passkey::lang.messages.registered'));

                return $plugin->renderPasskeyList($user);
            });

            // Delete a passkey
            $controller->addDynamicMethod('onPasskeyDelete', function () use ($controller, $plugin) {
                $user = $plugin->getTargetUser($controller);
                $credentialId = post('credential_id');

                WebauthnCredential::where('backend_user_id', $user->id)
                    ->where('id', (int) $credentialId)
                    ->delete();

                \Flash::success(trans('ihorchyshkala.passkey::lang.messages.deleted'));

                return $plugin->renderPasskeyList($user);
            });
        });
    }

    /**
     * Get the target user from the controller context (editing user or myaccount).
     * Uses URL param (not POST) and verifies permissions to prevent IDOR.
     */
    public function getTargetUser($controller): BackendUserModel
    {
        $currentUser = BackendAuth::getUser();
        if (!$currentUser) {
            throw new \ApplicationException('Not authenticated.');
        }

        // Myaccount: always return current user, ignore any POST user_id
        if ($controller->getAction() === 'myaccount') {
            return $currentUser;
        }

        // Editing another user: require admins.manage permission
        if (!$currentUser->hasAccess('admins.manage')) {
            throw new \ApplicationException('Access denied.');
        }

        // Use controller URL param only (not POST data) to prevent tampering
        $userId = $controller->params[0] ?? null;

        if (!$userId) {
            throw new \ApplicationException('User not found.');
        }

        return BackendUserModel::findOrFail($userId);
    }

    /**
     * Render the passkey list partial (used for AJAX refreshes)
     */
    public function renderPasskeyList(BackendUserModel $user): array
    {
        $credentials = WebauthnCredential::where('backend_user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            '#passkey-list-container' => $this->makePartialContents(
                plugins_path('ihorchyshkala/passkey/partials/_passkey_list'),
                ['credentials' => $credentials, 'userId' => $user->id]
            ),
        ];
    }

    /**
     * Render a partial and return its contents as a string.
     * Uses a closure to isolate scope instead of extract().
     */
    protected function makePartialContents(string $path, array $vars = []): string
    {
        $render = static function (string $_path, array $_vars) {
            extract($_vars, EXTR_SKIP);
            ob_start();
            include $_path . '.php';
            return ob_get_clean();
        };

        return $render($path, $vars);
    }
}
