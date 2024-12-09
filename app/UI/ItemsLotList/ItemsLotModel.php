<?php
namespace App\UI\ItemsLotList;

use \App\UI\Entities\ItemWithLot;
use \App\UI\Entities\WarehouseHasItem;
use \App\UI\Exceptions\NotFoundException;
use \App\UI\Exceptions\UsedNameException;
use \App\UI\ItemsLotList\ItemLotIsUsedException;
use \Nette\Http\IRequest;

class ItemsLotModel
{
    public function __construct(
            protected \App\UI\Model\Database $dbe, 
            protected \Doctrine\ORM\EntityManager $em, 
            protected \App\UI\ItemsList\ItemsModelFactory $items_model_factory, 
            protected \App\UI\Model\SqlPaginatorFactory $sql_paginator_factory
    )
    {
        
    }
    
    public function getItemWithLotList(IRequest $request, int $item_id)
    {
        $this->items_model_factory->create()->getItem($item_id);
        
        $filters = new \App\UI\ItemsLotList\ItemsLotListFilers();
        $adapter = new \App\UI\TableFilters\QueryBuilderToSqlAdapter();
        $filters->applyFilters($adapter, $request);
        if ($request->getQuery('sort_by') != 'il.id') {
            $adapter->addOrderBy('il.id', 'ASC');
        }
        
        $sql = "SELECT il.*, ilu.n AS used   
                FROM item_with_lot il 
                LEFT JOIN 
                (
                    SELECT item_with_lot_id AS id, COUNT(item_with_lot_id) AS n 
                    FROM warehouse_has_item 
                    GROUP BY item_with_lot_id
                ) AS ilu ON il.id = ilu.id 
                WHERE il.item_id = :item_id {$adapter->getWhereTerm(false)} {$adapter->getOrderByTerm(true)}";
        
        $parameters = array_merge([':item_id' => $item_id], $adapter->getParameters());
        $list_page = $this->sql_paginator_factory->create($sql, $parameters, 15, $request);
        return $list_page;
    }
    
    public function getItemWithLot(int $item_lot_id): ItemWithLot
    {
        $item_lot = $this->em->getRepository(ItemWithLot::class)->findOneById($item_lot_id);
        if (!$item_lot) {
            throw new NotFoundException('polozka se sarzi nenalezena', NotFoundException::ITEMWITHLOT);
        }
        return $item_lot;
    }
    
    public function getItemWithLotByItemId(int $item_id, string $lot): ItemWithLot
    {
        $this->items_model_factory->create()->getItem($item_id);
        $result = $this->em->getRepository(ItemWithLot::class)->findOneBy(
                [
                    'item_id' => $item_id, 
                    'lot' => $lot
                ]
        );
        
        if (!$result) {
            throw new NotFoundException('polozka se sarzi nenalezena', NotFoundException::ITEMWITHLOT);
        }
        return $result;
    }
    
    public function createItemWithLot(int $item_id, string $lot): ItemWithLot
    {
        $this->checkNameIsUsed($item_id, $lot);
        $item = $this->items_model_factory->create()->getItem($item_id);
        $item_wWith_lot = new ItemWithLot();
        $item_wWith_lot
                ->setItem($item)
                ->setlot($lot)
                ->setAdded((new \DateTime()))
                ;
        $this->em->persist($item_wWith_lot);
        $this->em->flush();
        return $item_wWith_lot;
    }
    
    public function getOrCreateItemWithLot(int $item_id, string $lot): ItemWithLot
    {
        try {
           return $this->getItemWithLotByItemId($item_id, $lot);
        } catch (NotFoundException $e) {
            if ($e->getCode() != NotFoundException::ITEMWITHLOT) {
                throw $e;
            }
            return $this->createItemWithLot($item_id, $lot);
        }
    }
    
    public function deleteAllFromItem(int $item_id)
    {
        $this->items_model_factory->create()->checkIfItemIsUsed($item_id, false);
        
        $lots = $this->em->getRepository(ItemWithLot::class)->findBy(['item_id' => $item_id]);
        foreach ($lots as $lot) {
            $this->em->remove($lot);
        }        
        
        $this->em->flush();
    }
    
    public function renameItemLot(int $item_lot_id, string $new_lot)
    {
        $item_lot = $this->getItemWithLot($item_lot_id);
        $this->checkNameIsUsed($item_lot->getItemId(), $new_lot);
        $item_lot->setLot($new_lot);
        $this->em->flush();
    }
    
    public function deleteItemLot(int $item_lot_id)
    {
        $item_lot = $this->getItemWithLot($item_lot_id);
        $this->checkItemLotIsUsed($item_lot_id);
        $this->em->remove($item_lot);
        $this->em->flush();
    }
    
    public function newItemLot(int $item_id, string $lot)
    {
        $this->checkNameIsUsed($item_id, $lot);
        $item = $this->items_model_factory->create()->getItem($item_id);
        $new_item_lot = new ItemWithLot();
        $new_item_lot
                ->setLot($lot)
                ->getItem($item)
                ->setAdded(new \DateTime())
                ;
        $this->em->persist($new_item_lot);
        $this->em->flush();
    }
    
    protected function checkNameIsUsed(int $item_id, string $lot)
    {
        try {
            $this->getItemWithLotByItemId($item_id, $lot);
            throw new UsedNameException('Toto jmeno sarze pro danou polozku je jiz vyuzito');
        } catch (NotFoundException $e) {
            if ($e->getCode() != NotFoundException::ITEMWITHLOT) {
                throw $e;
            }
        }
    }
    
    protected function checkItemLotIsUsed(int $item_id)
    {
        $item_lot_in_warehouse = $this->em
                ->getRepository(WarehouseHasItem::class)
                ->findOneBy(['item_with_lot_id' => $item_id])
                ;
        if ($item_lot_in_warehouse) {
            throw new ItemLotIsUsedException('sarze je jiz pouzita');
        }
    }
    
}
