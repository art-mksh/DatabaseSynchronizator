<?php

namespace DatabaseSynchronizator\ColumnDefinitionComparator;

/**
 * ColumnDefinitionComparatorInterface - Интерфейс для вспомогательного класса  для проверки разницы в столбцах
 */
interface ColumnDefinitionComparatorInterface
{
    /**
     * isColumnDefinitionDifferent - Метод для проверки описания столбцов на различия
     *
     * @param string sourceDefinition
     * @param string targetDefinition
     *
     * @return bool
     */
    public function isColumnDefinitionDifferent(string $sourceDefinition, ?string $targetDefinition = null): bool;
}
