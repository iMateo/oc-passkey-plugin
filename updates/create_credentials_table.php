<?php

namespace IHORCHYSHKALA\Passkey\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateCredentialsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('ihorchyshkala_passkey_credentials')) {
            Schema::create('ihorchyshkala_passkey_credentials', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->integer('backend_user_id')->unsigned()->index();
                $table->text('credential_id');
                $table->text('public_key');
                $table->string('name')->default('');
                $table->integer('sign_count')->unsigned()->default(0);
                $table->text('transports')->nullable();
                $table->timestamps();

                $table->foreign('backend_user_id')
                    ->references('id')
                    ->on('backend_users')
                    ->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('ihorchyshkala_passkey_credentials');
    }
}
