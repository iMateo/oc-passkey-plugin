<?php

namespace IHORCHYSHKALA\Passkey\Models;

use Model;

class WebauthnCredential extends Model
{
    protected $table = 'ihorchyshkala_passkey_credentials';

    protected $guarded = ['id'];

    /**
     * backend_user_id is set explicitly, never via mass assignment.
     */

    protected $casts = [
        'sign_count' => 'integer',
    ];

    public $belongsTo = [
        'backend_user' => [\Backend\Models\User::class, 'key' => 'backend_user_id'],
    ];

    public function getTransportsArrayAttribute()
    {
        if (empty($this->transports)) {
            return [];
        }

        return json_decode($this->transports, true) ?: [];
    }

    public function setTransportsArrayAttribute($value)
    {
        $this->attributes['transports'] = is_array($value) ? json_encode($value) : $value;
    }
}
