<?php

namespace IHORCHYSHKALA\Passkey\Updates;

use Log;
use Schema;
use October\Rain\Database\Updates\Migration;

/**
 * Make credential_id unique: a credential may belong to exactly one backend user.
 * Duplicate rows (if any) are removed first, keeping the oldest (lowest id).
 * Idempotent: does nothing when a unique index on credential_id already exists.
 */
class AddCredentialIdUniqueIndex extends Migration
{
    const TABLE = 'ihorchyshkala_passkey_credentials';
    const UNIQUE_INDEX = 'ihorchyshkala_passkey_cred_id_unique';

    public function up()
    {
        if (!Schema::hasTable(self::TABLE)) {
            return;
        }

        // MySQL/MariaDB: binary ASCII collation, so lookups and the unique index are
        // case-sensitive (base64url) and the key is 512 bytes instead of 2048 (utf8mb4).
        // Done first so de-duplication below uses the same comparison as the index.
        $this->convertCredentialIdToAsciiBin();

        $indexes = $this->getIndexes();

        foreach ($indexes as $index) {
            if ($index['unique'] && $index['columns'] === ['credential_id']) {
                return;
            }
        }

        $this->removeDuplicateCredentials();

        Schema::table(self::TABLE, function ($table) use ($indexes) {
            // The plain index from 1.0.1 becomes redundant
            if (isset($indexes['idx_credential_id'])) {
                $table->dropIndex('idx_credential_id');
            }

            $table->unique('credential_id', self::UNIQUE_INDEX);
        });
    }

    public function down()
    {
        if (!Schema::hasTable(self::TABLE)) {
            return;
        }

        $indexes = $this->getIndexes();

        Schema::table(self::TABLE, function ($table) use ($indexes) {
            if (isset($indexes[self::UNIQUE_INDEX])) {
                $table->dropUnique(self::UNIQUE_INDEX);
            }

            if (!isset($indexes['idx_credential_id'])) {
                $table->index('credential_id', 'idx_credential_id');
            }
        });
    }

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

    protected function removeDuplicateCredentials(): void
    {
        $connection = Schema::getConnection();

        $duplicates = $connection->table(self::TABLE)
            ->select('credential_id')
            ->groupBy('credential_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('credential_id');

        foreach ($duplicates as $credentialId) {
            $ids = $connection->table(self::TABLE)
                ->where('credential_id', $credentialId)
                ->orderBy('id')
                ->pluck('id')
                ->all();

            $keepId = array_shift($ids);
            if (!$ids) {
                continue;
            }

            $connection->table(self::TABLE)->whereIn('id', $ids)->delete();

            Log::warning('Passkey: removed duplicate credential rows before adding unique index', [
                'kept_row_id' => $keepId,
                'deleted_row_ids' => $ids,
            ]);
        }
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
