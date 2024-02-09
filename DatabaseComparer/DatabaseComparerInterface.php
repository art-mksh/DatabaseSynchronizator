<?php

namespace DatabaseSynchronizator\DatabaseComparer;

use DatabaseSynchronizator\DatabaseAdapter\DatabaseAdapterInterface;
use DatabaseSynchronizator\DifferenceStrategy\DifferenceStrategyInterface;

/**
 * DatabaseComparerInterface - Контекстный интерфейс для паттерна стратегия
 * для получения разницы между база данных используя выбранную стратегию
 */
interface DatabaseComparerInterface
{
    /**
     * getDifferences - метод для получения разницы между базами данных испольуя стратегию
     *
     * @param DatabaseAdapterInterface sourceDatabase
     * @param DatabaseAdapterInterface targetDatabase
     * @param DifferenceStrategyInterface differenceStrategy
     *
     * @return array
     */
    public function getDifferences(
        DatabaseAdapterInterface $sourceDatabase,
        DatabaseAdapterInterface $targetDatabase,
        DifferenceStrategyInterface $differenceStrategy
    ): array;
}
