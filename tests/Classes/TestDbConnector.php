<?php
namespace tests\Classes;

use \Nette\Neon\Neon;
use \Doctrine\DBAL\DriverManager;
use \Doctrine\DBAL\Connection;
use \Doctrine\Migrations\DependencyFactory;
use \Doctrine\Migrations\MigratorConfiguration;

//require_once 'bootstrap.php';

/**
 * pro ucely testovaci vytvori testovaci DB (tu predchozi smaze)
 * nutne:
 *      -v config/database_connection.neon MUSI byt nastaveno jmeno testovaci DB, jako ['database_connection']['testDbname'], jinak dojde v vymazu dat z ostre DB
 *      - createContainer() v bootstrap.php musi vracet DI, kde je nacten i config/test.neon, jinak dojde v vymazu dat z ostre DB
 *      - musi existovat Doctrine migrace (migrations:dump-schema), vc. prikazu, co vlozi radky do tabulek item_status a order_status
 */

class TestDbConnector
{
    private array $defaultDbConnParams;
    private string $testDbName;

    public function __construct()
    {
        $databaseNeon = Neon::decodeFile(__DIR__ . '/../../config/database_connection.neon');
        $this->defaultDbConnParams = $databaseNeon['parameters']['database_connection'];
        $this->testDbName = $this->defaultDbConnParams['testDbname'];
    }
    
    public function setup()
    {
        $conn = $this->getDbConnectionWoDbName();
        $conn->executeStatement("DROP DATABASE IF EXISTS `{$this->testDbName}`");
        $conn->executeStatement("CREATE DATABASE `{$this->testDbName}`");
        $conn->close();
        
        /** @var DependencyFactory $dependencyFactory */
        $dependencyFactory = createContainer()->getByType(DependencyFactory::class);
        $dependencyFactory->getMetadataStorage()->ensureInitialized();
        $migrationPlanCalculator = $dependencyFactory->getMigrationPlanCalculator();
        $upPlan = $migrationPlanCalculator->getPlanUntilVersion($migrationPlanCalculator->getMigrations()->getLast()->getVersion());
        $config = (new MigratorConfiguration())->setAllOrNothing(true);
        $dependencyFactory->getMigrator()->migrate($upPlan, $config);
    }
    
    private function getDbConnectionWoDbName(): Connection
    {
        return DriverManager::getConnection([
            'driver' => $this->defaultDbConnParams['driver'],
            'host' => $this->defaultDbConnParams['host'],
            'user' => $this->defaultDbConnParams['user'],
            'password' => $this->defaultDbConnParams['password'],
        ]);        
    }
}
