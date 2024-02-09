<?php

namespace DatabaseSynchronizator\DifferenceStrategy;

use DatabaseSynchronizator\DatabaseAdapter\DatabaseAdapterInterface;

/**
 * DifferenceStrategyInterface - Интерфейс для классов с паттерном стратегия для вычисления разницы между базами данных
 */
interface DifferenceStrategyInterface
{
    /**
     * getDifferences - получение разницы между базами данных в формате массива
     *
     * @param DatabaseAdapterInterface $source
     * @param DatabaseAdapterInterface $target
     *
     * @return array
     */
    public function getDifferences(DatabaseAdapterInterface $source, DatabaseAdapterInterface $target): array;
}
