<?php

ini_set('display_errors', 1);

ini_set('display_startup_errors', 1);

error_reporting(E_ALL);

// Подключаем автоподгрузку классов
require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Создаем  контейнер для IoC
$containerBuilder = new \DI\ContainerBuilder();
$containerBuilder->useAutowiring(false);

$containerBuilder->addDefinitions([
    'ColumnDefinitionComparatorInterface' => \DI\create(DatabaseSynchronizator\ColumnDefinitionComparator\ColumnDefinitionComparatorImpl::class),
]);

$containerBuilder->addDefinitions([
    'DatabaseAdapterInterface' => \DI\create(DatabaseSynchronizator\DatabaseAdapter\MySQLAdapter::class),
]);

$containerBuilder->addDefinitions([
    'DatabaseComparerInterface' => \DI\create(DatabaseSynchronizator\DatabaseComparer\DatabaseComparerImpl::class),
]);

$containerBuilder->addDefinitions([
    'DifferenceStrategyInterface' => \DI\create(DatabaseSynchronizator\DifferenceStrategy\DatabaseDifferenceStrategy::class)
        ->constructor(\DI\get('ColumnDefinitionComparatorInterface')),
]);

$containerBuilder->addDefinitions([
    'SqlGeneratorInterface' => \DI\create(DatabaseSynchronizator\SqlGenerator\SqlGeneratorImpl::class)->constructor(DI\get('DatabaseAdapterInterface')),
]);

// Определяем данные баз данных
$servername = $_ENV['DB_HOST'];
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASS'];
$sourceDatabaseName = $_ENV['SOURCE_DB_NAME'];

// Собираем контейнер с зависимостями для IoC
$container = $containerBuilder->build();

// Создаем подключение
$sourceDatabaseConnection = new \mysqli($servername, $username, $password, $sourceDatabaseName);

// Проверяем подключение
if ($sourceDatabaseConnection->connect_error) {
    die("Connection failed: " . $sourceDatabaseConnection->connect_error);
}

// Добавляем подключение в адаптер
$sourceDatabase = new DatabaseSynchronizator\DatabaseAdapter\MySQLAdapter($sourceDatabaseConnection);

$targetDatabaseName = $_ENV['TARGET_DB_NAME'];

// Создаем подключение
$targetDatabaseConnection = new \mysqli($servername, $username, $password, $targetDatabaseName);

// Проверяем подключение
if ($targetDatabaseConnection->connect_error) {
    die("Connection failed: " . $targetDatabaseConnection->connect_error);
}

// Добавляем подключение в адаптер
$targetDatabase = new DatabaseSynchronizator\DatabaseAdapter\MySQLAdapter($targetDatabaseConnection);

// Создаем синхронизатор
$DatabaseSynchonizer = new DatabaseSynchronizator\DatabaseSynchronization(
    $container->get('DatabaseComparerInterface'),
    $container->get('SqlGeneratorInterface'),
    $container->get('DifferenceStrategyInterface')
);

// Синхронизируем базы данных
$DatabaseSynchonizer->synchronizeTargetDatabaseAccordingToSource(
    $sourceDatabase,
    $targetDatabase
);
