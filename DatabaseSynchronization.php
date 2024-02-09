<?php

namespace DatabaseSynchronizator;

use DatabaseSynchronizator\SqlGenerator\SqlGeneratorInterface;
use DatabaseSynchronizator\DatabaseComparer\DatabaseComparerInterface;
use DatabaseSynchronizator\DifferenceStrategy\DifferenceStrategyInterface;
use DatabaseSynchronizator\DatabaseAdapter\DatabaseAdapterInterface;

/**
 * DatabaseSynchronizator - Класс для синхронизации баз данных
 */
class DatabaseSynchronization
{

    private $databaseComparer;
    private $databaseDifferenceStrategy;
    private $sqlGenerator;

    public function __construct(
        DatabaseComparerInterface $databaseComparer,
        SqlGeneratorInterface $sqlGenerator,
        DifferenceStrategyInterface $databaseDifferenceStrategy
    ) {
        $this->databaseComparer = $databaseComparer;
        $this->databaseDifferenceStrategy = $databaseDifferenceStrategy;
        $this->sqlGenerator = $sqlGenerator;
    }

    /**
     * synchronize - метод для синхронизации структуры второй базы данных по примеру
     * первой без удаления несовпадающих колонок и таблиц.
     * Колонки и таблицы, которые не совпадают, автоматически сохраняются с префиксом OLD
     *
     * @param DatabaseAdapterInterface sourceDatabase
     * @param DatabaseAdapterInterface targetDatabase
     *
     * @return void
     */
    public function synchronizeTargetDatabaseAccordingToSource(
        DatabaseAdapterInterface $sourceDatabase,
        DatabaseAdapterInterface $targetDatabase
    ): void {
        $databasesDifferences = $this->databaseComparer->getDifferences(
            $sourceDatabase,
            $targetDatabase,
            $this->databaseDifferenceStrategy
        );
        $sqlCommandsToUpdateDifferencesInTargetDatabase = $this->sqlGenerator->generateSql($databasesDifferences);
        foreach ($sqlCommandsToUpdateDifferencesInTargetDatabase as $SqlCommand) {
            $targetDatabase->query($SqlCommand);
        }
    }
}
