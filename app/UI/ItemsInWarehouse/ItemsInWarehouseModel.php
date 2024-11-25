<?php
namespace App\UI\ItemsInWarehouse;

use \App\UI\Tools\ArrayTools;

class ItemsInWarehouseModel
{
    public function __construct(
            protected \Nette\Database\Explorer $dbe, 
            protected \Doctrine\ORM\EntityManager $em,             
            protected \App\UI\ItemsList\ItemsModelFactory $items_model_factory, 
            protected \App\UI\WarehouseList\WarehouseModelFactory $warehouse_model_factory
    )
    {
    
    }
    
    public function getList(bool $available_only)
    {
        $item_status_term = $available_only ? "= 'available'" : "IN ('available', 'reserved')";
        $list = $this->em->createQuery(
                "SELECT w.id AS warehouse_id, w.name AS warehouse, it.name AS item, COUNT(il.item_id) AS n 
                FROM App\\UI\\Entities\\WarehouseHasItem wi 
                JOIN wi.warehouse w 
                JOIN wi.item_with_lot il 
                JOIN il.item it 
                JOIN wi.status its 
                WHERE its.short_name {$item_status_term} 
                GROUP BY wi.warehouse_id, il.item_id 
                ORDER BY wi.warehouse_id, il.item_id"
        )->getResult();
        
        return ArrayTools::groupMultiArray($list, 'warehouse');
    }
    
    public function getEmptyWarehousesListForSelect(bool $available_items_only)
    {
        $item_status_term = $available_items_only ? "= 'available'" : "IN ('available', 'reserved')";
        
        return $this->dbe->query(
                "SELECT w.id, w.name 
                FROM warehouse w 
                LEFT JOIN 
                (
                    SELECT wi.* 
                    FROM warehouse_has_item wi 
                    JOIN item_status ist ON wi.status_id 
                    WHERE ist.short_name {$item_status_term}
                ) AS wi ON wi.warehouse_id = w.id 
                WHERE wi.id IS NULL"
        )->fetchPairs();
    }
    
    public function getItemMaxAmount(int $warehouse_id, int $item_id): int
    {
        $item = $this->items_model_factory->create()->getItem($item_id);
        $warehouse_model = $this->warehouse_model_factory->create();        
        $warehouse_free_space = $warehouse_model->getFreeSpace($warehouse_id);
        return floor($warehouse_free_space / $item->getArea());
    }
    
}
