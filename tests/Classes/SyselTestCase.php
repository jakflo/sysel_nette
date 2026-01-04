<?php
namespace tests\Classes;

//require_once 'bootstrap.php';

class SyselTestCase extends \Tester\TestCase
{
    public function __construct()
    {
        $dbConnector = new \tests\TestDbConnector();
        $dbConnector->setup();
    }
    
    protected function setUp()
    {
        $cacheDriver = new \Doctrine\Common\Cache\ArrayCache();
        $cacheDriver->deleteAll();
        $this->em->getConnection()->beginTransaction();
    }
    
    protected function tearDown()
    {
        $this->em->getConnection()->rollBack();
    }
    
    public function run(): void
    {
        $dbConnector = new \tests\Classes\TestDbConnector();
        $dbConnector->setup();
        parent::run();
    }
}
