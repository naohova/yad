<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require_once 'vendor/autoload.php';

$dbParams = [
    'driver' => 'pdo_pgsql',
    'host' => '127.0.0.1',
    'port' => '5432',
    'dbname' => 'postgres',
    'user' => 'postgres',
    'password' => 'root',
];

$config = Setup::createAttributeMetadataConfiguration(
    [__DIR__ . '/src/Entity'],
    true
);

$entityManager = EntityManager::create($dbParams, $config);

return ConsoleRunner::createHelperSet($entityManager); 