<?php

namespace IHORCHYSHKALA\Passkey\Classes;

/**
 * User-facing passkey error. Only this exception type is allowed to reach the
 * client with its message; anything else is logged and replaced by a generic text.
 */
class PasskeyException extends \October\Rain\Exception\ApplicationException
{
}
