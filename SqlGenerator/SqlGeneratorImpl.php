<?php

namespace DatabaseSynchronizator\SqlGenerator;

use DatabaseSynchronizator\SqlGenerator\SqlGeneratorInterface;

/**
 * SqlGenerator - Класс для генерации строк запросов
 */
class SqlGeneratorImpl implements SqlGeneratorInterface
{
    /**
     * generateSql  - общий метод формирующий все SQL команды на основе различий в БД
     *
     * @param array diff
     *
     * @return array
     */
    public function generateSql(array $diff): array
    {
        $sqlTransactions = [];
        if (isset($diff['deletedTables'])) {
            $sqlTransactions = array_merge($sqlTransactions, $this->backUpTablesSql($diff['deletedTables']));
        }
        if (isset($diff['newTables'])) {
            $sqlTransactions = array_merge($sqlTransactions, $this->generateTablesSql($diff['newTables']));
        }
        $databaseBackedUpColumns = [];
        if (isset($diff['modifiedColumns'])) {
            $databaseBackedUpColumns = array_merge($databaseBackedUpColumns, $diff['modifiedColumns']);
        }
        if (isset($diff['deletedColumns'])) {
            $databaseBackedUpColumns = array_merge($databaseBackedUpColumns, $diff['deletedColumns']);
        }
        $sqlTransactions = array_merge($sqlTransactions, $this->backUpColumnsSql($databaseBackedUpColumns));
        $databaseChangedColumns = [];
        if (isset($diff['modifiedColumns'])) {
            $databaseChangedColumns = array_merge($databaseChangedColumns, $diff['modifiedColumns']);
        }
        if (isset($diff['newColumns'])) {
            $databaseChangedColumns = array_merge($databaseChangedColumns, $diff['newColumns']);
        }
        $sqlTransactions = array_merge($sqlTransactions, $this->generateColumnsSql($databaseChangedColumns));
        return $sqlTransactions;
    }

    /**
     * generateTablesSql  - метод для генерации SQL для добавления новых таблиц
     *
     * @param array newTables
     *
     * @return array
     */
    private function generateTablesSql(array $newTables): array
    {
        $sql = [];
        foreach ($newTables as $table => $columns) {
            $sqlString = "CREATE TABLE `$table` (";
            $columnsLength = count($columns);
            $columnsCounter = 0;
            foreach ($columns as $columnName => $definition) {
                $sqlString .= $columnName . ' ' . $this->buildColumnDefinition($definition);
                if ($columnsCounter != ($columnsLength--)) {
                    $sqlString .= ',';
                }
                $columnsCounter++;
            }
            $sqlString .= "); ";
            $sql[] = $sqlString;
        }
        return $sql;
    }

    /**
     * generateColumnsSql - метод для генерации SQL для добавления новых столбцов
     *
     * @param array newColumns
     *
     * @return array
     */
    private function generateColumnsSql(array $newColumns): array
    {
        $sql = [];
        foreach ($newColumns as $table => $columns) {
            foreach ($columns as $column => $definition) {
                $sql[] = "ALTER TABLE `$table` ADD COLUMN `$column` "
                    . $this->buildColumnDefinition($definition) . "; ";
            }
        }
        return $sql;
    }

    /**
     * backUpColumnsSql
     *
     * @param array modifiedColumns
     *
     * @return array
     */
    private function backUpColumnsSql(array $modifiedColumns): array
    {
        $sql = [];
        foreach ($modifiedColumns as $table => $columns) {
            foreach ($columns as $column => $definition) {
                $sql[] = "ALTER TABLE `$table` RENAME COLUMN `$column` TO `"
                    . $this->buildSavedColumnName($column) . "` ; ";
            }
        }
        return $sql;
    }


    /**
     * backUpTablesSql
     *
     * @param array deletedTables
     *
     * @return array
     */
    private function backUpTablesSql(array $deletedTables): array
    {
        $sql = [];
        foreach ($deletedTables as $table) {
            $sql[] = "ALTER TABLE `$table` RENAME TO `" . $this->buildSavedTableName($table) . "`; ";
        }
        return $sql;
    }

    /**
     * buildColumnDefinition
     *
     * @param array definition
     *
     * @return string
     */
    private function buildColumnDefinition(array $definition): string
    {
        $sql = $definition['Type'];
        $sql .= (isset($definition["Null"]) && $definition["Null"] == 'YES') ? ' Null' : '';
        $sql .= (isset($definition["Default"]) && $definition["Default"]) ? $definition["Default"] : '';
        $sql .= (isset($definition["Extra"]) && $definition["Extra"] != '') ? ' ' . $definition["Extra"] : '';
        return $sql;
    }

    /**
     * buildSavedColumnName
     *
     * @param string column
     *
     * @return string
     */
    private function buildSavedColumnName(string $column): string
    {
        return 'OLD_' . $column;
    }

    /**
     * buildSavedTableName
     *
     * @param string table
     *
     * @return string
     */
    private function buildSavedTableName(string $table): string
    {
        return 'OLD_' . $table;
    }
}
