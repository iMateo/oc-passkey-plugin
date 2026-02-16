<?php

namespace IHORCHYSHKALA\Passkey\Controllers;

use Backend\Classes\Controller;

/**
 * Passkey controller - lightweight AJAX endpoint hub.
 * Methods are invoked via addDynamicMethod on Auth and Users controllers.
 */
class Passkey extends Controller
{
    public $implement = [];

    public $requiredPermissions = [];
}
