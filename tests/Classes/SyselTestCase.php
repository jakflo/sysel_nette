<?php
namespace tests\Classes;

//require_once 'bootstrap.php';

class SyselTestCase extends \Tester\TestCase
{
    public function __construct(
        protected \Doctrine\ORM\EntityManager $em,
        protected TestDbConnector $testDbConnector
    )
    {
        $this->testDbConnector->setup();
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
        $this->testDbConnector->setup();
        parent::run();
    }
}
