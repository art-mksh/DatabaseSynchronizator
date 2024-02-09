<?php

namespace DatabaseSynchronizator\DatabaseAdapter;

/**
 * DatabaseAdapterInterface - Интерфейс для адаптера базы данных
 */
interface DatabaseAdapterInterface
{

    /**
     * query - Метод для запроса к базе данных
     *
     * @param string query
     *
     * @return array
     */
    public function query(string $query): array;
    /**
     * getCurrentDatabaseTables - Вспомогательный метод для получения всех таблиц выбранной базы данных
     *
     * @return array
     */
    public function getCurrentDatabaseTables(): array;
    /**
     * getSelectedTableColumns - Вспомогательный метод для получения всех столбцов выбранной таблицы
     *
     * @param string tableName
     *
     * @return array
     */
    public function getSelectedTableColumns(string $tableName): array;
}
