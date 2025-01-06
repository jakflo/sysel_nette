<?php
namespace App\UI\ItemsInWarehouse;

use \App\UI\Tools\ArrayTools;
use \App\UI\ItemsInWarehouse\WarehouseCapacityExceededException;
use \App\UI\Entities\ItemStatus;
use \App\UI\Entities\WarehouseHasItem;
use \App\UI\Tools\ProtectedIn;

class ItemsInWarehouseModel
{
    public function __construct(
            protected \App\UI\Model\Database $db, 
            protected \Doctrine\ORM\EntityManager $em, 
            protected \App\UI\ItemsList\ItemsModelFactory $items_model_factory, 
            protected \App\UI\ItemsLotList\ItemsLotModelFactory $items_lot_model_factory, 
            protected \App\UI\WarehouseList\WarehouseModelFactory $warehouse_model_factory
    )
    {
    
    }
    
    public function getList(bool $available_only, array|null $warehouses_id = null, array|null $items_id = null)
    {
        $item_status_term = $available_only ? "= 'available'" : "IN ('available', 'reserved')";
        $warehouses_term = $warehouses_id ? "AND w.id IN(:wid)" : "";
        $items_term = $items_id ? "AND it.id IN(:itid)" : "";
        $list = $this->em->createQuery(
                "SELECT w.id AS warehouse_id, w.name AS warehouse, it.id AS item_id, it.name AS item, COUNT(il.item_id) AS n 
                FROM App\\UI\\Entities\\WarehouseHasItem wi 
                JOIN wi.warehouse w 
                JOIN wi.item_with_lot il 
                JOIN il.item it 
                JOIN wi.status its 
                WHERE its.short_name {$item_status_term} {$warehouses_term} {$items_term} 
                GROUP BY wi.warehouse_id, il.item_id 
                ORDER BY wi.warehouse_id, il.item_id"
        );

        if ($warehouses_id) {
            $list->setParameter('wid', $warehouses_id);
        }
        if ($items_id) {
            $list->setParameter('itid', $items_id);
        }
        $result = $list->getResult();
        
        return ArrayTools::groupMultiArray($result, 'warehouse');
    }
        
    public function getEmptyWarehousesListForSelect(bool $available_items_only)
    {
        $item_status_term = $available_items_only ? "= 'available'" : "IN ('available', 'reserved')";
        
        return $this->db->fetchPairs(
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
        );
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
    
    public function changeItemsStatuses(string $item_status_short_name, int|null $order_id, array $warehouse_has_item_id)
    {
        $item_status_id = $this->em->getRepository(ItemStatus::class)->findOneBy(['short_name' => $item_status_short_name])->getId();
        $warehouse_has_item_id_term = new ProtectedIn();
        $warehouse_has_item_id_term->addArray('wid', $warehouse_has_item_id);
        
        $this->db->executeQuery(
            "UPDATE warehouse_has_item SET order_id = :oid, status_id = :sid WHERE id IN({$warehouse_has_item_id_term->getTokens('wid')})", 
            array_merge(
                [
                    'oid' => $order_id, 
                    'sid' => $item_status_id
                ], 
                $warehouse_has_item_id_term->getData()
            )
        );
    }
    
}
