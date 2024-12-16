<?php
namespace App\UI\Tests;

use \Tester\Assert;
use \App\UI\Entities\ItemWithLot;
use \App\UI\Exceptions\UsedNameException;
use \App\UI\Exceptions\NotFoundException;
use \App\UI\ItemsLotList\ItemLotIsUsedException;

class ItemsLot extends \App\UI\Tests\TestCase
{
    public function __construct(
        protected \App\UI\Tests\DataCreatorFactory $data_creator_factory, 
        protected \Doctrine\ORM\EntityManager $em,         
        protected \Nette\Http\IRequest $http_request, 
        protected \App\UI\ItemsInWarehouse\ItemsInWarehouseModelFactory $items_in_warehouse_model_factory, 
        protected \App\UI\ItemsLotList\ItemsLotModelFactory $items_lot_model_factory, 
        protected \App\UI\ItemsList\ItemsModelFactory $items_model_factory
    )
    {
        
    }
    
    public function testCreateTwoNewItemsInWarehouse()
    {
        $data_creator = $this->data_creator_factory->create();
        $warehouse_id = $data_creator->createWarehouse('w_fjkdsjfiwieeffds');
        $item_id = $data_creator->createItem('it_fjkdsjfiwieeffds');
        $lot_name = 'lo_fjkdsjfiwieeffds';
        
        $items_in_warehouse_model = $this->items_in_warehouse_model_factory->create();
        $items_in_warehouse_model->addItems($warehouse_id, $item_id, $lot_name, 5);
        $items_lot_1 = $this->em->getRepository(ItemWithLot::class)->findBy(['lot' => $lot_name]);
        Assert::count(1, $items_lot_1);
        
        $items_in_warehouse_model->addItems($warehouse_id, $item_id, $lot_name, 5);
        $items_lot_2 = $this->em->getRepository(ItemWithLot::class)->findBy(['lot' => $lot_name]);
        Assert::count(1, $items_lot_2);
    }
    
    public function testCreateLot()
    {
        $data_creator = $this->data_creator_factory->create();
        $item_id = $data_creator->createItem('it_fjkdsjfiwieeffds');
        $items_lot_model = $this->items_lot_model_factory->create();
        $lot_name = 'lo_fjkdsjfiwieeffds';
        
        $items_lot_model->createItemWithLot($item_id, $lot_name);
        $items_lot_model->getItemWithLotByItemId($item_id, $lot_name);
        Assert::exception(
            function() use($items_lot_model, $item_id, $lot_name)  {
                $items_lot_model->createItemWithLot($item_id, $lot_name);
            }, 
            UsedNameException::class
        );
    }
    
    public function testRenameLot()
    {
        $data_creator = $this->data_creator_factory->create();
        $item_id = $data_creator->createItem('it_fjkdsjfiwieeffds');
        $items_lot_model = $this->items_lot_model_factory->create();
        $lot_name_1 = 'lo_fjkdsjfiwieeffds_1';
        $lot_name_2 = 'lo_fjkdsjfiwieeffds_2';
        
        $item_lot_id = $items_lot_model->createItemWithLot($item_id, $lot_name_1)->getId();
        $items_lot_model->renameItemLot($item_lot_id, $lot_name_2);
        $items_lot_model->getItemWithLotByItemId($item_id, $lot_name_2);
        
        Assert::exception(
            function() use($items_lot_model, $item_lot_id, $lot_name_2)  {
                $items_lot_model->renameItemLot($item_lot_id, $lot_name_2);
            }, 
            UsedNameException::class
        );
    }
    
    public function testDeleteLot()
    {
        $data_creator = $this->data_creator_factory->create();
        $items_lot_model = $this->items_lot_model_factory->create();
        
        $warehouse_id = $data_creator->createWarehouse('w_fjkdsjfiwieeffds');
        $item_id = $data_creator->createItem('it_fjkdsjfiwieeffds');
        $item_lot_1 = $items_lot_model->createItemWithLot($item_id, 'lo_fjkdsjfiwieeffds_1');
        $items_lot_model->deleteItemLot($item_lot_1->getId());
        Assert::exception(
            function() use($items_lot_model, $item_id) {
                $items_lot_model->getItemWithLotByItemId($item_id, 'lo_fjkdsjfiwieeffds_1');
            }, 
            NotFoundException::class, 
            null, 
            NotFoundException::ITEMWITHLOT
        );
        
        $item_lot_2 = $items_lot_model->createItemWithLot($item_id, 'lo_fjkdsjfiwieeffds_2');
        $items_in_warehouse_model = $this->items_in_warehouse_model_factory->create();
        $items_in_warehouse_model->addItems($warehouse_id, $item_id, 'lo_fjkdsjfiwieeffds_2', 2);
        Assert::exception(
            function() use($items_lot_model, $item_lot_2) {
                $items_lot_model->deleteItemLot($item_lot_2->getId());
            }, 
            ItemLotIsUsedException::class
        );
    }
    
    public function testDeleteLotWhileItemIsDeleted()
    {
        $data_creator = $this->data_creator_factory->create();
        $items_lot_model = $this->items_lot_model_factory->create();
        $items_model = $this->items_model_factory->create();
        
        $item_id = $data_creator->createItem('it_fjkdsjfiwieeffds');
        $items_lot_model->createItemWithLot($item_id, 'l_ffhjshdsusa_1');
        $items_lot_model->createItemWithLot($item_id, 'l_ffhjshdsusa_2');
        $items_lot_model->createItemWithLot($item_id, 'l_ffhjshdsusa_3');
        $items_lot_model->createItemWithLot($item_id, 'l_ffhjshdsusa_4');
        $items_lot_model->createItemWithLot($item_id, 'l_ffhjshdsusa_5');
        
        $items_model->delete($item_id);
        $items_lot = $this->em->getRepository(ItemWithLot::class)->findBy(['item_id' => $item_id]);
        Assert::count(0, $items_lot);
    }
    
    public function testFullList()
    {
        $data_creator = $this->data_creator_factory->create();
        $items_lot_model = $this->items_lot_model_factory->create();
        $lot_name = 'l_kldjsaljffewkhfjkasqw';
        
        $item_id = $data_creator->createItem('i_kldjsaljffewkhfjkasqw');
        $items_lot_model->createItemWithLot($item_id, $lot_name);
        $url = $this->http_request->getUrl()
                ->withQueryParameter('lot_name_cond', 'equal')
                ->withQueryParameter('lot_name_value', $lot_name)                
                ->withQueryParameter('sort_by', 'lot_name')
                ->withQueryParameter('sort_desc', '1')
                ->withQueryParameter('page', '1')
                ;
        
        $http_request = new \Nette\Http\Request($url);
        $items_page = $items_lot_model->getItemWithLotList($http_request, $item_id);
        Assert::count(1, $items_page->getRows());
    }
    
}
