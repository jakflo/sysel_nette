<?php
namespace App\UI\WarehouseList;

use \App\UI\Exceptions\UsedNameException;
use \App\UI\Exceptions\NotFoundException;
use \App\UI\WarehouseList\NotEmptyException;
use \App\UI\Entities\Warehouse;

class WarehouseModel
{
    public function __construct(
            protected \Nette\Database\Explorer $dbe, 
            protected \Doctrine\ORM\EntityManager $em
    )
    {
        
    }
    
    public function printList()
    {
        $warehouses = $this->dbe->query(
                "SELECT w.*, wa.area_filled
                FROM warehouse w
                LEFT JOIN 
                (
                    SELECT wi.warehouse_id, SUM(i.area) AS area_filled
                    FROM warehouse_has_item wi 
                    JOIN item_with_lot il ON wi.item_with_lot_id = il.id 
                    JOIN item i ON il.item_id = i.id 
                    JOIN item_status it ON wi.status_id = it.id
                    WHERE it.short_name IN ('available', 'reserved')
                    GROUP BY wi.warehouse_id
                ) AS wa ON wa.warehouse_id = w.id
                ORDER BY w.id"
        )->fetchAll();        
        return $warehouses;
    }
    
    public function printSimpleLIstForSelect()
    {
        return $this->dbe->query("SELECT id, name FROM Warehouse ORDER BY id")->fetchPairs();
    }
    
    public function rename(int $id, string $new_name)
    {
        $this->checkIfNameIsUsed($new_name);        
        $warehouse = $this->getWarehouse($id);
        
        $warehouse
                ->setName($new_name)
                ->setLastEdited((new \DateTime()))
                ;
        $this->em->flush();
    }
    
    public function delete(int $id)
    {
        $warehouse = $this->getWarehouse($id);
        
        $warehouse_first_item = $this->em
                ->createQuery("SELECT iw 
                                FROM App\\UI\\Entities\\WarehouseHasItem iw 
                                JOIN iw.status s 
                                WHERE s.short_name IN('available', 'reserved') 
                                    AND iw.warehouse_id = :wid")
                ->setParameters(['wid' => $id])
                ->setMaxResults(1)
                ->getResult()                
                ;
        if (count($warehouse_first_item) > 0) {
            throw new NotEmptyException('Sklad neni prazdny');
        }
        
        $this->em->remove($warehouse);
        $this->em->flush();
    }
    
    public function create(string $name, int $area)
    {
        $this->checkIfNameIsUsed($name);
        
        $warehouse = (new Warehouse())
                ->setName($name)
                ->setArea($area)
                ->setCreated((new \DateTime()))
                ;
        $this->em->persist($warehouse);
        $this->em->flush();
    }
    
    public function getUsedSpace(int $id): float
    {
        $this->getWarehouse($id);
        $warehouse_used_space = $this->em
                ->createQuery("SELECT SUM(i.area) AS n 
                                FROM App\\UI\\Entities\\WarehouseHasItem iw 
                                JOIN iw.status s 
                                JOIN iw.item_with_lot il 
                                JOIN il.item i 
                                WHERE s.short_name IN('available', 'reserved') 
                                    AND iw.warehouse_id = :wid")
                ->setParameters(['wid' => $id])                
                ->getOneOrNullResult();
        return $warehouse_used_space['n']?? 0;
    }
    
    public function getFreeSpace(int $id): float
    {
        $warehouse = $this->getWarehouse($id);
        $used_space = $this->getUsedSpace($id);
        return $warehouse->getArea() - $used_space;
    }
    
    protected function checkIfNameIsUsed(string $name)
    {
        if ($this->em->getRepository(Warehouse::class)->findOneByName($name)) {
            throw new UsedNameException('Jmeno jiz pouzito');
        }
    }
    
    public function getWarehouse(int $id): Warehouse
    {
        $warehouse = $this->em->getRepository(Warehouse::class)->findOneById($id);
        if (!$warehouse) {
            throw new NotFoundException('Sklad nenalezen', NotFoundException::WAREHOUSE);
        }
        
        return $warehouse;
    }
    
}
