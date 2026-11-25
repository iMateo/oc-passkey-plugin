<?php

namespace IHORCHYSHKALA\Passkey\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

/**
 * Idempotent: safe to re-run (e.g. when a version history is collapsed and
 * this migration is replayed on a database that already has the index).
 */
class AddCredentialIdIndex extends Migration
{
    const TABLE = 'ihorchyshkala_passkey_credentials';

    public function up()
    {
        if (!Schema::hasTable(self::TABLE)) {
            return;
        }

        // Change TEXT to VARCHAR(512) so we can add a proper index.
        // Base64url-encoded credential IDs are typically 44-88 chars.
        // MySQL/MariaDB: ASCII binary collation (case-sensitive, 512-byte key that also
        // fits the 767-byte index limit of old servers); same helper as in 1.0.2.
        // Other drivers: ->change() (Laravel 9/10 uses doctrine/dbal, required by october/rain v3).
        if (in_array(Schema::getConnection()->getDriverName(), ['mysql', 'mariadb'], true)) {
            $this->convertCredentialIdToAsciiBin();
        }
        elseif ($this->credentialIdIsText()) {
            Schema::table(self::TABLE, function ($table) {
                $table->string('credential_id', 512)->change();
            });
        }

        // Any index on credential_id (including the unique one from 1.0.2) is enough
        if (!$this->hasCredentialIdIndex()) {
            Schema::table(self::TABLE, function ($table) {
                $table->index('credential_id', 'idx_credential_id');
            });
        }
    }

    public function down()
    {
        if (!Schema::hasTable(self::TABLE)) {
            return;
        }

        $indexes = $this->getIndexes();

        if (isset($indexes['idx_credential_id'])) {
            Schema::table(self::TABLE, function ($table) {
                $table->dropIndex('idx_credential_id');
            });
        }

        if (!$this->hasCredentialIdIndex() && !$this->credentialIdIsText()) {
            Schema::table(self::TABLE, function ($table) {
                $table->text('credential_id')->change();
            });
        }
    }

    /**
     * Duplicated in add_credential_id_unique_index.php (migration files are loaded
     * individually, no shared class).
     */
    protected function convertCredentialIdToAsciiBin(): void
    {
        $connection = Schema::getConnection();

        if (!in_array($connection->getDriverName(), ['mysql', 'mariadb'], true)) {
            return;
        }

        $table = $connection->getTablePrefix() . self::TABLE;

        $column = $connection->selectOne(
            'SELECT DATA_TYPE, CHARACTER_MAXIMUM_LENGTH, COLLATION_NAME FROM information_schema.COLUMNS'
            . ' WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
            [$table, 'credential_id']
        );

        if (
            $column
            && strtolower((string) $column->DATA_TYPE) === 'varchar'
            && (int) $column->CHARACTER_MAXIMUM_LENGTH === 512
            && strtolower((string) $column->COLLATION_NAME) === 'ascii_bin'
        ) {
            return;
        }

        $connection->statement(
            'ALTER TABLE `' . str_replace('`', '``', $table) . '`'
            . ' MODIFY `credential_id` VARCHAR(512) CHARACTER SET ascii COLLATE ascii_bin NOT NULL'
        );
    }

    protected function credentialIdIsText(): bool
    {
        try {
            $type = strtolower((string) Schema::getColumnType(self::TABLE, 'credential_id'));
        } catch (\Throwable $e) {
            // Unknown: let the (idempotent) column change run
            return true;
        }

        return in_array($type, ['text', 'tinytext', 'mediumtext', 'longtext', 'clob'], true);
    }

    protected function hasCredentialIdIndex(): bool
    {
        foreach ($this->getIndexes() as $index) {
            if ($index['columns'] === ['credential_id']) {
                return true;
            }
        }

        return false;
    }

    /**
     * Index list as [lowercase name => ['unique' => bool, 'columns' => [...]]].
     * Laravel 10.x+/12 expose Schema::getIndexes(); older Laravel 9/10 use doctrine/dbal.
     */
    protected function getIndexes(): array
    {
        $connection = Schema::getConnection();
        $builder = $connection->getSchemaBuilder();
        $result = [];

        if (method_exists($builder, 'getIndexes')) {
            foreach ($builder->getIndexes(self::TABLE) as $index) {
                $result[strtolower($index['name'])] = [
                    'unique' => (bool) $index['unique'],
                    'columns' => array_map('strtolower', $index['columns']),
                ];
            }

            return $result;
        }

        $manager = $connection->getDoctrineSchemaManager();
        foreach ($manager->listTableIndexes($connection->getTablePrefix() . self::TABLE) as $name => $index) {
            $result[strtolower($name)] = [
                'unique' => $index->isUnique(),
                'columns' => array_map('strtolower', $index->getColumns()),
            ];
        }

        return $result;
    }
}
