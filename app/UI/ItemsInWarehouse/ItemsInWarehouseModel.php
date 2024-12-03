<?php
namespace App\UI\ItemsInWarehouse;

use \App\UI\Tools\ArrayTools;
use \App\UI\ItemsInWarehouse\WarehouseCapacityExceededException;
use \App\UI\Entities\ItemStatus;
use \App\UI\Entities\WarehouseHasItem;

class ItemsInWarehouseModel
{
    public function __construct(
            protected \Nette\Database\Explorer $dbe, 
            protected \Doctrine\ORM\EntityManager $em, 
            protected \App\UI\ItemsList\ItemsModelFactory $items_model_factory, 
            protected \App\UI\ItemsLotList\ItemsLotModelFactory $items_lot_model_factory, 
            protected \App\UI\WarehouseList\WarehouseModelFactory $warehouse_model_factory
    )
    {
    
    }
    
    public function getList(bool $available_only)
    {
        $item_status_term = $available_only ? "= 'available'" : "IN ('available', 'reserved')";
        $list = $this->em->createQuery(
                "SELECT w.id AS warehouse_id, w.name AS warehouse, it.id AS item_id, it.name AS item, COUNT(il.item_id) AS n 
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
    
    public function addItems(int $warehouse_id, int $item_id, string $lot_name, int $amount)
    {
        $warehouse_model = $this->warehouse_model_factory->create();
        $warehouse = $warehouse_model->getWarehouse($warehouse_id);
        
        $max_amount = $this->getItemMaxAmount($warehouse_id, $item_id);
        if ($amount > $max_amount) {
            throw new WarehouseCapacityExceededException('Tolik polozek se do skladu nevejde');
        }
        
        $items_lot_model = $this->items_lot_model_factory->create();
        $item_with_lot = $items_lot_model->getOrCreateItemWithLot($item_id, $lot_name);
        $item_status = $this->em->getRepository(ItemStatus::class)->findOneBy(['short_name' => 'available']);
        
        $added_items = [];
        for ($c = 1; $c <= $amount; $c++) {
            $added_item = new WarehouseHasItem();
            $added_item
                    ->setWarehouse($warehouse)
                    ->setItemWithLot($item_with_lot)
                    ->setOrderId(null)
                    ->setStatus($item_status)
                    ->setAdded((new \DateTime()))
                    ;
            $this->em->persist($added_item);
            $added_items[] = $added_item;
        }
        
        $this->em->flush();
    }
    
}
