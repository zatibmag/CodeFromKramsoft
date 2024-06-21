<?php

namespace App\DBAL\Migrations;

use Doctrine\Migrations\AbstractMigration as BaseAbstractMigration;

abstract class AbstractMigration extends BaseAbstractMigration
{
    protected function abortIfNotMySql(): void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'sqlite',
            'This migration is designed only for SQLite DB platform.'
        );
    }

    protected function abortIfTableExists($names): void
    {
        $this->abortIfTable($names, true);
    }

    protected function abortIfTableMissing($names): void
    {
        $this->abortIfTable($names, false);
    }

    private function abortIfTable($names, $exists): void
    {
        if (!is_array($names)) {
            trigger_deprecation(
                'app',
                '210811',
                'Passing $names as a string to "%s()" is deprecated, pass an array of string instead.',
                __METHOD__
            );

            $names = (array)$names;
        }

        $abortMessage = $exists
            ? 'This migration will create %s table which already exists.'
            : 'This migration requires %s table.';

        foreach ($names as $name) {
            $this->abortIf($exists === $this->hasTable($name), sprintf($abortMessage, $name));
        }
    }

    public function skipIfTablesExist(array $names): void
    {
        foreach ($names as $name) {
            if ($this->hasTable($name)) {
                $this->skipIf(true, 'Tables do exist');
            }
        }
    }

    public function skipIfTablesNotExist(array $names): void
    {
        foreach ($names as $name) {
            if ($this->hasTable($name)) {
                return;
            }
        }

        $this->skipIf(true, 'Tables do not exist');
    }

    private function hasTable(string $name): bool
    {
        return false !== $this->connection->executeQuery('SELECT tbl_name FROM sqlite_master where tbl_name LIKE ?', [$name], ['string'])->fetchOne();
    }

    public function hasColumn(string $tableName, string $columnName): bool
    {
        $sql    = <<<SQL
SELECT COLUMN_NAME
  FROM information_schema.columns
  WHERE table_schema = ? AND table_name = ? AND column_name LIKE ?
SQL;
        $params = [$this->connection->getDatabase(), $tableName, $columnName];
        $types  = ['string', 'string', 'string'];

        return false !== $this->connection->executeQuery($sql, $params, $types)->fetchOne();
    }

    protected function columnHasKey(string $tableName, string $columnName): bool
    {
        $sql    = <<<SQL
SELECT COUNT(*)
  FROM information_schema.statistics
 WHERE table_schema = ? AND table_name = ? AND column_name = ?
SQL;
        $params = [$this->connection->getDatabase(), $tableName, $columnName];
        $types  = ['string', 'string', 'string'];

        return $this->connection->executeQuery($sql, $params, $types)->fetchOne() > 0;
    }

    protected function columnExists(string $tableName, string $column): bool
    {
        return $this->connection
                ->executeQuery('SHOW COLUMNS FROM `' . $tableName . '` LIKE ?', [$column], ['string'])
                ->rowCount() > 0;
    }

    protected function addSqlIf(bool $condition, string $sql, array $params = [], array $types = []): void
    {
        if ($condition) {
            $this->addSql($sql, $params, $types);
        }
    }

    protected function dropForeignKey(string $table, string $column, string $refTable, string $refColumn): void
    {
        $sql    = <<<SQL
SELECT CONSTRAINT_NAME
  FROM information_schema.KEY_COLUMN_USAGE
 WHERE TABLE_SCHEMA = ?
       AND TABLE_NAME = ?
       AND COLUMN_NAME = ?
       AND REFERENCED_TABLE_SCHEMA = ?
       AND REFERENCED_TABLE_NAME = ?
       AND REFERENCED_COLUMN_NAME = ?
SQL;
        $dbname = $this->connection->getDatabase();
        $params = [$dbname, $table, $column, $dbname, $refTable, $refColumn];
        $names  = $this->connection->executeQuery($sql, $params)->fetchFirstColumn();
        $uFound = count(array_unique($names));
        if ($uFound !== 1) {
            throw new \RuntimeException(__FUNCTION__ . ' was expected to find 1 unique key; found ' . $uFound);
        }
        $uName = current($names);
        $this->addSql('ALTER TABLE ' . $table . ' DROP FOREIGN KEY ' . $uName);
    }

    protected function dropIndex(string $table, array $columns, bool $unique): void
    {
        $columnWhere = implode(' OR ', array_fill(0, count($columns), 'COLUMN_NAME = ?'));

        $sql    = <<<SQL
SELECT INDEX_NAME
  FROM information_schema.STATISTICS
 WHERE TABLE_SCHEMA = ?
       AND TABLE_NAME = ?
       AND ($columnWhere)
       AND NON_UNIQUE = ?
SQL;
        $dbname = $this->connection->getDatabase();
        $params = array_merge([$dbname, $table], $columns, [(int)!$unique]);
        $names  = $this->connection->executeQuery($sql, $params)->fetchFirstColumn();
        $uFound = count(array_unique($names));
        if ($uFound !== 1) {
            throw new \RuntimeException(__FUNCTION__ . ' was expected to find 1 unique key; found ' . $uFound);
        }
        $uName = current($names);

        $sql             = <<<SQL
SELECT COLUMN_NAME
  FROM information_schema.STATISTICS
 WHERE TABLE_SCHEMA = ?
       AND INDEX_NAME = ?
 ORDER BY COLUMN_NAME
SQL;
        $result          = $this->connection->executeQuery($sql, [$dbname, $uName]);
        $expectedColumns = $result->fetchFirstColumn();
        if ([] !== array_diff($expectedColumns, $columns)) {
            $message = __FUNCTION__ . ' was expected $columns to contains "' . implode('", "', $expectedColumns)
                . '" columns; got: "' . implode('", "', $columns) . '"';
            throw new \RuntimeException($message);
        }

        $this->addSql('DROP INDEX ' . $uName . ' ON ' . $table);
    }
}
