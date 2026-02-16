<?php

namespace IHORCHYSHKALA\Passkey\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class AddCredentialIdIndex extends Migration
{
    public function up()
    {
        Schema::table('ihorchyshkala_passkey_credentials', function ($table) {
            // Change TEXT to VARCHAR(512) so we can add a proper index.
            // Base64url-encoded credential IDs are typically 44-88 chars.
            $table->string('credential_id', 512)->change();
        });

        Schema::table('ihorchyshkala_passkey_credentials', function ($table) {
            $table->index('credential_id', 'idx_credential_id');
        });
    }

    public function down()
    {
        Schema::table('ihorchyshkala_passkey_credentials', function ($table) {
            $table->dropIndex('idx_credential_id');
            $table->text('credential_id')->change();
        });
    }
}
