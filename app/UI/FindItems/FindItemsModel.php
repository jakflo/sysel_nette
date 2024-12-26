<?php
namespace App\UI\FindItems;

use \App\UI\Exceptions\NotFoundException;

class FindItemsModel
{
    public function __construct(
            protected \Doctrine\ORM\EntityManager $em
    )
    {
        
    }
    
    public function checkWarehousesExist(array $warehouses_id)
    {
        $warehouses_id = array_unique($warehouses_id);
        $found_warehouses = $this->em->createQuery("SELECT COUNT(w.id) AS n FROM App\\UI\\Entities\\Warehouse w WHERE w.id IN(:wids)")
                ->setParameter('wids', $warehouses_id)
                ->getOneOrNullResult()
                ;
        
        if ($found_warehouses['n'] < count($warehouses_id)) {
            throw new NotFoundException('sklad nenalezen', NotFoundException::WAREHOUSE);
        }
    }
    
    public function checkItemsExist(array $items_id)
    {
        $items_id = array_unique($items_id);
        $found_items = $this->em->createQuery("SELECT COUNT(it.id) AS n FROM App\\UI\\Entities\\Item it WHERE it.id IN(:iids)")
                ->setParameter('iids', $items_id)
                ->getOneOrNullResult()
                ;
        
        if ($found_items['n'] < count($items_id)) {
            throw new NotFoundException('sklad nenalezen', NotFoundException::ITEM);
        }
    }
}
