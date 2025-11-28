<?php
namespace App\UI\ItemsList;

use \App\UI\Entities\Item;
use \App\UI\ItemsList\ItemIsUsedException;
use \App\UI\Exceptions\NotFoundException;
use \App\UI\Exceptions\UsedNameException;

class ItemsModel
{
    public function __construct(
            protected \App\UI\Model\Database $db, 
            protected \Doctrine\ORM\EntityManager $em, 
            protected \App\UI\ManufacturerList\ManufacturerModelFactory $manufacturer_model_factory, 
            protected \App\UI\ItemsLotList\ItemsLotModelFactory $items_lot_model_factory
    )
    {
        
    }
    
    public function printList(bool $available_only = false)
    {
        $shortnames = $available_only ? "'available'" : "'available', 'reserved'";
        return $this->db->fetchAllObjects(
                "SELECT i.*, m.name AS manufacturer, a.country, iu.items_stored, iu2.items_used
                FROM item i 
                JOIN manufacturer m ON i.manufacturer_id = m.id 
                JOIN address a ON m.address_id = a.id
                LEFT JOIN (
                    SELECT i.id, COUNT(i.id) AS items_stored
                    FROM warehouse_has_item wi 
                    JOIN item_status it ON wi.status_id = it.id 
                    JOIN item_with_lot il ON wi.item_with_lot_id = il.id 
                    JOIN item i ON il.item_id = i.id
                    WHERE it.short_name IN ({$shortnames})
                    GROUP BY i.id
                ) AS iu ON iu.id = i.id
                LEFT JOIN (
                    SELECT i.id, COUNT(i.id) AS items_used
                    FROM warehouse_has_item wi 
                    JOIN item_with_lot il ON wi.item_with_lot_id = il.id 
                    JOIN item i ON il.item_id = i.id
                    GROUP BY i.id
                ) AS iu2 ON iu2.id = i.id"
        );
    }
    
    public function printSimpleList(): array
    {
        return $this->db->fetchPairs("SELECT id, name FROM item ORDER BY id");
    }
    
    public function printItemStateList()
    {
        return $this->db->fetchPairs("SELECT id, name FROM item_status ORDER BY id");
    }
    
    public function changeArea(int $item_id, float $area)
    {
        $this->checkIfItemIsUsed($item_id, true);
        $item = $this->getItem($item_id);
        $item->setArea($area);
        $this->em->flush();
    }
    
    public function changeName(int $item_id, string $name)
    {
        $this->checkIfNameIsUsed($name);
        $item = $this->getItem($item_id);
        $item->setName($name);
        $this->em->flush();
    }
    
    public function delete(int $item_id)
    {
        $this->checkIfItemIsUsed($item_id, false);
        $item = $this->getItem($item_id);
        $this->items_lot_model_factory->create()->deleteAllFromItem($item_id);
        $this->em->remove($item);
        $this->em->flush();
    }
    
    public function create(string $name, float $area, int $manufacturer_id): int
    {
        $this->checkIfNameIsUsed($name);
        $manufacturer = $this->manufacturer_model_factory->create()->getManufacturer($manufacturer_id);
        
        $item = new Item();
        $item
                ->setName($name)
                ->setArea($area)
                ->setAdded((new \DateTime()))
                ->setManufacturer($manufacturer)
                ;
        $this->em->persist($item);
        $this->em->flush();
        return $item->getId();
    }
    
    public function checkIfItemIsUsed(int $item_id, bool $stored_items_only)
    {
        $statuse_term = $stored_items_only ? "AND s.short_name IN ('available', 'reserved')" : '';
        $item_is_used = $this->em->createQuery(
                "SELECT wi 
                FROM App\\UI\\Entities\\WarehouseHasItem wi 
                JOIN wi.status s 
                JOIN wi.item_with_lot il                 
                WHERE il.item_id = :item_id {$statuse_term}"
        )
                ->setParameters(['item_id' => $item_id])
                ->setMaxResults(1)
                ->getResult();
                
        if (count($item_is_used) > 0) {
            throw new ItemIsUsedException('Polozka je jiz pouzita');
        }
    }
    
    public function getItem(int $item_id): Item
    {
        $item = $this->em->getRepository(Item::class)->findOneById($item_id);
        if (!$item) {
            throw new NotFoundException('polozka nenalezena', NotFoundException::ITEM);
        }
        return $item;
    }
    
    public function getItems(array $items_id): array
    {
        if (count($items_id) === 0) {
            throw new \Exception('pole nemuze byt prazdne');
        }
        
        return $this->em->createQuery(
            "SELECT it 
            FROM App\\UI\\Entities\\Item it 
            WHERE it.id IN(:iid)"
        )
            ->setParameter('iid', $items_id)
            ->getResult()
        ;
    }
    
    protected function checkIfNameIsUsed(string $name)
    {
        if ($this->em->getRepository(Item::class)->findOneByName($name)) {
            throw new UsedNameException('Jmeno jiz pouzito');
        }
    }
    
}
