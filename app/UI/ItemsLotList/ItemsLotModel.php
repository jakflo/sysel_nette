<?php
namespace App\UI\ItemsLotList;

use \App\UI\Entities\ItemWithLot;
use \App\UI\Exceptions\NotFoundException;
use \App\UI\Exceptions\UsedNameException;

class ItemsLotModel
{
    public function __construct(
            protected \Nette\Database\Explorer $dbe, 
            protected \Doctrine\ORM\EntityManager $em, 
            protected \App\UI\ItemsList\ItemsModelFactory $items_model_factory
    )
    {
        
    }
    
    public function getItemWithLot(int $item_id, string $lot): ItemWithLot
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
           return $item_wWith_lot = $this->getItemWithLot($item_id, $lot);
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
    
    protected function checkNameIsUsed(int $item_id, string $lot)
    {
        try {
            $this->getItemWithLot($item_id, $lot);
            throw new UsedNameException('Toto jmeno sarze pro danou polozku je jiz vyuzito');
        } catch (NotFoundException $e) {
            if ($e->getCode() != NotFoundException::ITEMWITHLOT) {
                throw $e;
            }
        }
    }
    
}
