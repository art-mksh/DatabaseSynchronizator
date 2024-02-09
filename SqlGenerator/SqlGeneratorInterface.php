<?php

namespace DatabaseSynchronizator\SqlGenerator;

/**
 * SqlGeneratorInterface - Интерефейс для генерации строк запросов
 */
interface SqlGeneratorInterface
{
    /**
     * generateSql - Генерирует запросы по форматированию целевой БД для дальнейшего исполнения
     *
     * @param array diff
     *
     * @return array
     */
    public function generateSql(array $diff): array;
}
