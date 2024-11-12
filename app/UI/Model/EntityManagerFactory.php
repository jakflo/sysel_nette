<?php
namespace App\UI\Model;

interface EntityManagerFactory
{
    public function create(): \Doctrine\ORM\EntityManager;
}
