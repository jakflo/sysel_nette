<?php
namespace App\UI\Tests;

abstract class TestCase extends \Tester\TestCase
{
    protected \Doctrine\ORM\EntityManager $em;
    
    protected function setUp()
    {
        $this->em->getConnection()->beginTransaction();
    }
    
    protected function tearDown()
    {
        echo '<br />';
        $this->em->getConnection()->rollBack();
    }
    
}
