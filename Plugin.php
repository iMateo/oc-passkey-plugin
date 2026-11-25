<?php

namespace IHORCHYSHKALA\Passkey;

use Log;
use Event;
use Config;
use Backend;
use Session;
use BackendAuth;
use Backend\Models\User as BackendUserModel;
use Backend\Models\AccessLog;
use Backend\Controllers\Auth as AuthController;
use Backend\Controllers\Users as UsersController;
use Illuminate\Support\Facades\RateLimiter;
use IHORCHYSHKALA\Passkey\Classes\PasskeyException;
use IHORCHYSHKALA\Passkey\Models\WebauthnCredential;
use IHORCHYSHKALA\Passkey\Services\WebauthnService;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    /**
     * Required so the plugin boots on the protected backend/auth routes.
     */
    public $elevated = true;

    /**
     * Rate limits per minute: login options requests per IP, failed login
     * verifications per IP, wrong current-password attempts per user.
     */
    protected const LOGIN_OPTIONS_PER_MINUTE = 30;
    protected const LOGIN_VERIFY_FAILURES_PER_MINUTE = 10;
    protected const REGISTER_PASSWORD_FAILURES_PER_MINUTE = 5;

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
        $plugin = $this;
        AuthController::extend(function ($controller) use ($plugin) {
            $controller->addDynamicMethod('onPasskeyLoginOptions', function () use ($plugin) {
                return $plugin->guard('login options', 'errors.login_failed', function () use ($plugin) {
                    $ip = request()->ip();
                    $plugin->hitRateLimit('login_options:' . sha1((string) $ip), static::LOGIN_OPTIONS_PER_MINUTE, ['ip' => $ip]);

                    $service = new WebauthnService();

                    return ['options' => $service->createLoginOptions()];
                });
            });

            $controller->addDynamicMethod('onPasskeyLoginVerify', function () use ($plugin) {
                return $plugin->guard('login verify', 'errors.login_failed', function () use ($plugin) {
                    $ip = request()->ip();

                    // Counted before the check (no race between parallel requests) and
                    // cleared on success, so effectively only failures use up the limit
                    $failKey = 'login_verify_failed:' . sha1((string) $ip);
                    $plugin->hitRateLimit($failKey, static::LOGIN_VERIFY_FAILURES_PER_MINUTE, ['ip' => $ip]);

                    try {
                        $service = new WebauthnService();

                        $user = $service->verifyLogin(
                            post('credential_id'),
                            post('client_data_json'),
                            post('authenticator_data'),
                            post('signature'),
                            post('user_handle')
                        );

                        // Same ban / suspension rules as the password sign-in
                        $plugin->checkLoginThrottle($user);
                    } catch (\Throwable $e) {
                        // Detail stays in the log; the client always gets the same message
                        Log::warning('Passkey login failed', [
                            'ip' => $ip,
                            'exception' => get_class($e),
                            'error' => $e->getMessage(),
                        ]);
                        throw new PasskeyException(trans('ihorchyshkala.passkey::lang.errors.login_failed'));
                    }

                    // Parity with BackendAuth::authenticate()
                    $user->clearResetPassword();

                    // Log the user in (passwordless). Remember policy follows the core sign-in:
                    // backend.force_remember, and there is no "remember me" checkbox here.
                    BackendAuth::login($user, (bool) Config::get('backend.force_remember'));

                    // The user is signed in from here on: failures below must not turn
                    // into a "login failed" response
                    try {
                        RateLimiter::clear($plugin->rateLimitKey($failKey));

                        // New session id after privilege change (data, incl. auth persist code, is kept)
                        Session::regenerate();

                        // Log access
                        AccessLog::add($user);

                        Log::info('Passkey login successful', [
                            'user_id' => $user->id,
                            'ip' => $ip,
                        ]);
                    } catch (\Throwable $e) {
                        Log::error('Passkey login post-processing error', [
                            'user_id' => $user->id,
                            'ip' => $ip,
                            'exception' => get_class($e),
                            'error' => $e->getMessage(),
                        ]);
                    }

                    return [
                        'redirect' => Backend::url('backend'),
                    ];
                });
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

            // Start registration ceremony (own account only, current password required).
            // The challenge is only issued after the password check, so Verify cannot
            // succeed without it.
            $controller->addDynamicMethod('onPasskeyRegisterOptions', function () use ($controller, $plugin) {
                return $plugin->guard('registration options', 'errors.registration_failed', function () use ($controller, $plugin) {
                    $user = $plugin->getRegistrationUser($controller);
                    $plugin->confirmCurrentPassword($user, post('current_password'));

                    $service = new WebauthnService();

                    return ['options' => $service->createRegistrationOptions($user)];
                });
            });

            // Complete registration ceremony (own account only)
            $controller->addDynamicMethod('onPasskeyRegisterVerify', function () use ($controller, $plugin) {
                return $plugin->guard('registration verify', 'errors.registration_failed', function () use ($controller, $plugin) {
                    $user = $plugin->getRegistrationUser($controller);

                    try {
                        $service = new WebauthnService();

                        $credential = $service->verifyRegistration(
                            $user,
                            post('client_data_json'),
                            post('attestation_object'),
                            post('passkey_name', 'Passkey')
                        );
                    } catch (\Throwable $e) {
                        Log::warning('Passkey registration failed', [
                            'user_id' => $user->id,
                            'ip' => request()->ip(),
                            'exception' => get_class($e),
                            'error' => $e->getMessage(),
                        ]);
                        throw new PasskeyException(trans('ihorchyshkala.passkey::lang.errors.registration_failed'));
                    }

                    Log::info('Passkey registered', $plugin->auditContext($user, $credential->id));

                    \Flash::success(trans('ihorchyshkala.passkey::lang.messages.registered'));

                    return $plugin->renderPasskeyList($user);
                });
            });

            // Delete (revoke) a passkey; allowed while impersonating
            $controller->addDynamicMethod('onPasskeyDelete', function () use ($controller, $plugin) {
                return $plugin->guard('delete', 'errors.unexpected', function () use ($controller, $plugin) {
                    $user = $plugin->getRevocationUser($controller);
                    $credentialId = (int) post('credential_id');

                    $credential = WebauthnCredential::where('backend_user_id', $user->id)
                        ->where('id', $credentialId)
                        ->first();

                    if (!$credential) {
                        throw new PasskeyException(trans('ihorchyshkala.passkey::lang.errors.not_found'));
                    }

                    $credential->delete();

                    Log::info('Passkey deleted', $plugin->auditContext($user, $credentialId));

                    \Flash::success(trans('ihorchyshkala.passkey::lang.messages.deleted'));

                    return $plugin->renderPasskeyList($user);
                });
            });
        });
    }

    /**
     * Run a handler body. Only PasskeyException (our own texts) reaches the client;
     * anything else is logged and replaced by a generic message, so no exception
     * text (SQL, library internals) leaks through the AJAX error response.
     */
    public function guard(string $context, string $fallbackLangKey, callable $callback)
    {
        try {
            return $callback();
        } catch (PasskeyException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Passkey ' . $context . ' error', [
                'ip' => request()->ip(),
                'exception' => get_class($e),
                'error' => $e->getMessage(),
                'file' => $e->getFile() . ':' . $e->getLine(),
            ]);
            throw new PasskeyException(trans('ihorchyshkala.passkey::lang.' . $fallbackLangKey));
        }
    }

    /**
     * Audit log context: the real (impersonating) admin is the actor.
     */
    public function auditContext(BackendUserModel $target, $credentialRowId): array
    {
        $actor = BackendAuth::getRealUser();

        $context = [
            'actor_id' => $actor ? $actor->id : null,
            'target_user_id' => $target->id,
            'credential_row_id' => $credentialRowId,
            'ip' => request()->ip(),
        ];

        if (BackendAuth::isImpersonator()) {
            $current = BackendAuth::getUser();
            $context['impersonated_user_id'] = $current ? $current->id : null;
        }

        return $context;
    }

    public function rateLimitKey(string $key): string
    {
        return 'ihorchyshkala_passkey:' . $key;
    }

    /**
     * Count an attempt and throw if it exceeds the limit
     * (fixed 60 s window; the counter is incremented atomically by the cache).
     */
    public function hitRateLimit(string $key, int $maxPerMinute, array $logContext = []): void
    {
        if (RateLimiter::hit($this->rateLimitKey($key), 60) > $maxPerMinute) {
            Log::warning('Passkey rate limited', $logContext + ['key' => $key]);
            throw new PasskeyException(trans('ihorchyshkala.passkey::lang.errors.too_many_attempts'));
        }
    }

    /**
     * Re-authentication before adding a passkey; wrong passwords are rate limited per user.
     */
    public function confirmCurrentPassword(BackendUserModel $user, $password): void
    {
        // Counted before the check (no race between parallel requests), cleared on success
        $key = 'register_password_failed:' . $user->id;
        $this->hitRateLimit($key, static::REGISTER_PASSWORD_FAILURES_PER_MINUTE, ['user_id' => $user->id]);

        if (!is_string($password) || $password === '' || !$user->checkPassword($password)) {
            Log::warning('Passkey registration: wrong current password', [
                'user_id' => $user->id,
                'ip' => request()->ip(),
            ]);
            throw new PasskeyException(trans('ihorchyshkala.passkey::lang.errors.wrong_password'));
        }

        RateLimiter::clear($this->rateLimitKey($key));
    }

    /**
     * Apply the core ban / suspension check (see October\Rain\Auth\Manager::validateInternal)
     * to a user resolved by passkey, respecting the auth manager's useThrottle flag.
     * On success the failed-attempt counter is cleared, as the core does.
     *
     * @throws \October\Rain\Auth\AuthException when the user is banned or suspended
     */
    public function checkLoginThrottle(BackendUserModel $user): void
    {
        $auth = BackendAuth::getFacadeRoot();

        // useThrottle is protected and has no accessor; read it in the manager's scope
        $useThrottle = true;
        try {
            $useThrottle = (bool) \Closure::bind(function () {
                return $this->useThrottle;
            }, $auth, get_class($auth))();
        } catch (\Throwable $e) {
            // Keep throttling enabled if the property cannot be read
        }

        if ($useThrottle) {
            $throttle = $auth->findThrottleByUserId($user->getKey(), $auth->ipAddress);
            $throttle->check();
            $throttle->clearLoginAttempts();
        }
    }

    /**
     * Registration is only ever allowed for the signed-in user's own account,
     * regardless of permissions, and never while impersonating.
     */
    public function getRegistrationUser($controller): BackendUserModel
    {
        $currentUser = BackendAuth::getUser();
        if (!$currentUser) {
            throw new PasskeyException(trans('ihorchyshkala.passkey::lang.errors.access_denied'));
        }

        if (BackendAuth::isImpersonator()) {
            throw new PasskeyException(trans('ihorchyshkala.passkey::lang.errors.impersonating'));
        }

        if ($controller->getAction() !== 'myaccount') {
            $params = (array) $controller->getParams();
            $userId = $params[0] ?? null;

            if ((string) $userId !== (string) $currentUser->id) {
                throw new PasskeyException(trans('ihorchyshkala.passkey::lang.errors.own_account_only'));
            }
        }

        return $currentUser;
    }

    /**
     * Revocation: own passkeys always; another user's passkeys only with admins.manage
     * and only if that user is reachable through the Users controller's rank-restricted
     * form query (formExtendQuery -> applyRankPermissionsToQuery).
     * Target id comes from the URL param only, never from POST data.
     */
    public function getRevocationUser($controller): BackendUserModel
    {
        $currentUser = BackendAuth::getUser();
        if (!$currentUser) {
            throw new PasskeyException(trans('ihorchyshkala.passkey::lang.errors.access_denied'));
        }

        // Myaccount: always the current user, ignore any POST user_id
        if ($controller->getAction() === 'myaccount') {
            return $currentUser;
        }

        $params = (array) $controller->getParams();
        $userId = $params[0] ?? null;

        if (!$userId || !is_scalar($userId)) {
            throw new PasskeyException(trans('ihorchyshkala.passkey::lang.errors.access_denied'));
        }

        if ((string) $userId === (string) $currentUser->id) {
            return $currentUser;
        }

        if (!$currentUser->hasAccess('admins.manage')) {
            throw new PasskeyException(trans('ihorchyshkala.passkey::lang.errors.access_denied'));
        }

        try {
            $user = $controller->formFindModelObject($userId);
        } catch (\Throwable $e) {
            $user = null;
        }

        if (!$user instanceof BackendUserModel) {
            throw new PasskeyException(trans('ihorchyshkala.passkey::lang.errors.access_denied'));
        }

        return $user;
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
