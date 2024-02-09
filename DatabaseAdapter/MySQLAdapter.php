<?php

namespace DatabaseSynchronizator\DatabaseAdapter;

use DatabaseSynchronizator\DatabaseAdapter\DatabaseAdapterInterface;

/**
 * MySQLAdapter - Адаптер базы данных для MySql
 */
class MySQLAdapter implements DatabaseAdapterInterface
{
    private $db;

    public function __construct(\mysqli $db)
    {
        $this->db = $db;
    }

    /**
     * query - Метод для запроса к базе данных
     *
     * @param string query
     *
     * @return array
     */
    public function query(string $sql): array
    {
        $result = $this->db->query($sql);
        $rows = array();
        if (isset($result->num_rows) && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        return $rows;
    }

    /**
     * getCurrentDatabaseTables - Вспомогательный метод для получения всех таблиц выбранной базы данных
     *
     * @return array
     */
    public function getCurrentDatabaseTables(): array
    {
        $result = $this->db->query("SHOW TABLES");
        $tables = array();
        while ($row = $result->fetch_array()) {
            $tables[] = $row[0];
        }
        return $tables;
    }

    /**
     * getSelectedTableColumns - Вспомогательный метод для получения всех столбцов выбранной таблицы
     *
     * @param string tableName
     *
     * @return array
     */
    public function getSelectedTableColumns(string $tableName): array
    {
        $result = $this->db->query("SHOW COLUMNS FROM $tableName");
        $columns = array();
        while ($row = $result->fetch_assoc()) {
            $columns[$row['Field']] = $row;
        }
        return $columns;
    }
}
