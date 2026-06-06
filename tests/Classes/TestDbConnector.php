<?php
namespace tests\Classes;

//use \Nette\Neon\Neon;
use \Doctrine\DBAL\DriverManager;
use \Doctrine\DBAL\Connection;
use \Doctrine\Migrations\DependencyFactory;
use \Doctrine\Migrations\MigratorConfiguration;

//require_once 'bootstrap.php';

/**
 * pro ucely testovaci vytvori testovaci DB (tu predchozi smaze)
 * nutne:
 *      - testovaci DB je po kazdem testu smazana, jeji jmeno se nesmi shodovat s produkcni DB 
 *      - musi existovat Doctrine migrace (migrations:dump-schema), vc. prikazu, co vlozi radky do tabulek item_status a order_status
 */

class TestDbConnector
{
    public function __construct(
        protected string $driver, 
        protected string $host, 
        protected string $user, 
        protected string $password, 
        protected string $testDbName
    )
    {}
    
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
    
    protected function getDbConnectionWoDbName(): Connection
    {
        return DriverManager::getConnection([
            'driver' => $this->driver,
            'host' => $this->host,
            'user' => $this->user,
            'password' => $this->password,
        ]);        
    }
}
