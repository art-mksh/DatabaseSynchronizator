<?php

namespace DatabaseSynchronizator\ColumnDefinitionComparator;

use DatabaseSynchronizator\ColumnDefinitionComparator\ColumnDefinitionComparatorInterface;

/**
 * ColumnDefinitionComparator - Вспомогательный класс для проверки разницы в столбцах
 */
class ColumnDefinitionComparatorImpl implements ColumnDefinitionComparatorInterface
{
    /**
     * isColumnDefinitionDifferent - Проверка описания столбцов на различия
     *
     * @param string sourceDefinition
     * @param string targetDefinition
     *
     * @return bool
     */
    public function isColumnDefinitionDifferent(string $sourceDefinition, ?string $targetDefinition = null): bool
    {
        return isset($targetDefinition) && $sourceDefinition != $targetDefinition;
    }
}
