<?php
namespace App\UI\ItemsList;

use \App\UI\Entities\WarehouseHasItem;
use \App\UI\Entities\Item;
use \App\UI\Entities\Manufacturer;
use \App\UI\ItemsList\ItemIsUsedException;
use \App\UI\Exceptions\NotFoundException;
use \App\UI\Exceptions\UsedNameException;

class ItemsModel
{
    /**     
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    public function __construct(
            protected \Nette\Database\Explorer $dbe, 
            protected \App\UI\Model\EntityManagerFactory $db_factory, 
            protected \App\UI\ManufacturerList\ManufacturerModelFactory $manufacturer_model_factory
    )
    {
        $this->em = $this->db_factory->create();
    }
    
    public function printList()
    {
        return $this->dbe->query(
                "SELECT i.*, m.name AS manufacturer, a.country, iu.items_stored, iu2.items_used 
                FROM item i 
                JOIN manufacturer m ON i.manufacturer_id = m.id 
                JOIN address a ON m.address_id = a.id 
                LEFT JOIN (
                    SELECT i.id, COUNT(i.id) AS items_stored 
                    FROM warehouse_has_item wi 
                    JOIN item_status it ON wi.status_id = it.id 
                    JOIN item i ON wi.item_id = i.id 
                    WHERE it.short_name IN ('available', 'reserved') 
                    GROUP BY i.id
                ) AS iu ON iu.id = i.id 
                LEFT JOIN (
                    SELECT i.id, COUNT(i.id) AS items_used 
                    FROM warehouse_has_item wi 
                    JOIN item i ON wi.item_id = i.id 
                    GROUP BY i.id
                ) AS iu2 ON iu2.id = i.id"
        )->fetchAll();
    }
    
    public function printSimpleList(): array
    {
        return $this->dbe->query("SELECT id, name FROM item ORDER BY id")->fetchPairs();
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
        $this->em->remove($item);
        $this->em->flush();
    }
    
    public function create(string $name, float $area, int $manufacturer_id)
    {
        $this->checkIfNameIsUsed($name);
        $manufacturer = $this->manufacturer_model_factory->create()->setEntityManager($this->em)->getManufacturer($manufacturer_id);
        
        $item = new Item();
        $item
                ->setName($name)
                ->setArea($area)
                ->setManufacturer($manufacturer)
                ;
        $this->em->persist($item);
        $this->em->flush();
    }
    
    protected function checkIfItemIsUsed(int $item_id, bool $stored_items_only)
    {
        $statuse_term = $stored_items_only ? "AND s.short_name IN ('available', 'reserved')" : '';
        $item_is_used = $this->em->createQuery(
                "SELECT wi 
                FROM App\\UI\\Entities\\WarehouseHasItem wi 
                JOIN wi.status s 
                WHERE wi.item_id = :item_id {$statuse_term}"
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
    
    protected function checkIfNameIsUsed(string $name)
    {
        if ($this->em->getRepository(Item::class)->findOneByName($name)) {
            throw new UsedNameException('Jmeno jiz pouzito');
        }
    }
    
}
