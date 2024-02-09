<?php

namespace DatabaseSynchronizator\DifferenceStrategy;

use DatabaseSynchronizator\ColumnDefinitionComparator\ColumnDefinitionComparatorInterface;
use DatabaseSynchronizator\DatabaseAdapter\DatabaseAdapterInterface;
use DatabaseSynchronizator\DifferenceStrategy\DifferenceStrategyInterface;

/**
 * DatabaseDifferenceStrategy - Класс с паттерном стратегия для вычисления разницы между базами данных
 */
class DatabaseDifferenceStrategy implements DifferenceStrategyInterface
{
    private $columnDefinitionComparator;

    public function __construct(ColumnDefinitionComparatorInterface $columnDefinitionComparator)
    {
        $this->columnDefinitionComparator = $columnDefinitionComparator;
    }

    /**
     * getDifferences - получение разницы между базами данных в формате массива
     *
     * @param DatabaseAdapterInterface source
     * @param DatabaseAdapterInterface target
     *
     * @return array
     */
    public function getDifferences(DatabaseAdapterInterface $source, DatabaseAdapterInterface $target): array
    {
        $differences = array();
        $sourceTables = $source->getCurrentDatabaseTables();
        $targetTables = $target->getCurrentDatabaseTables();
        $newTables = array_diff($sourceTables, $targetTables);
        foreach ($newTables as $table) {

            $differences['newTables'][$table] = $source->getSelectedTableColumns($table);
        }
        $differences['deletedTables'] = array_diff($targetTables, $sourceTables);
        foreach ($sourceTables as $table) {

            if (in_array($table, $targetTables)) {
                $sourceColumns = $source->getSelectedTableColumns($table);
                $targetColumns = $target->getSelectedTableColumns($table);
                $differences['newColumns'][$table] = array_diff_key($sourceColumns, $targetColumns);
                $differences['deletedColumns'][$table] = array_diff_key($targetColumns, $sourceColumns);
                foreach ($sourceColumns as $column => $definition) {
                    if (
                        isset($targetColumns[$column])
                        &&
                        $this->columnDefinitionComparator->isColumnDefinitionDifferent(
                            implode('', $definition),
                            implode('', $targetColumns[$column])
                        )
                    ) {
                        $differences['modifiedColumns'][$table][$column] = $definition;
                    }
                }
            }
        }
        return $differences;
    }
}
