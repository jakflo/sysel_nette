<?php
namespace App\UI\Tests;

use \Tester\Assert;
use \App\UI\Tools\ArrayTools;
use \App\UI\Entities\Warehouse;
use \App\UI\Entities\WarehouseHasItem;
use \App\UI\ItemsInWarehouse\WarehouseCapacityExceededException;

class ItemsInWarehouseList extends \App\UI\Tests\TestCase
{
    public function __construct(
            protected \App\UI\Tests\DataCreatorFactory $data_creator_factory, 
            protected \Doctrine\ORM\EntityManager $em, 
            protected \App\UI\ItemsInWarehouse\ItemsInWarehouseModelFactory $items_in_warehouse_model_factory, 
            protected \App\UI\ItemsList\ItemsModelFactory $items_model_factory, 
            protected \App\UI\ItemsLotList\ItemsLotModelFactory $items_lot_model_factory, 
            protected \App\UI\ItemsInWarehouseFull\ItemsInWarehouseFullModelFactory $items_in_warehouse_full_model_factory, 
            protected \Nette\Http\IRequest $http_request
    )
    {
        
    }
    
    public function testBriefListOne()
    {
        $this->doBriefListTest(false);
    }
    
    public function testBriefListTwo()
    {
        $this->doBriefListTest(true);
    }
    
    public function testAddItemToWarehouse()
    {
        $data_creator = $this->data_creator_factory->create();
        $items_in_warehouse_model = $this->items_in_warehouse_model_factory->create();
        $items_lot_model = $this->items_lot_model_factory->create();
        $warehouse_id = $data_creator->createWarehouse('w_dflkldskdsaeewiejq');
        $item_id = $data_creator->createItem('i_fffsdlkjddsw');
        $lot_name = 'l_fffsdlkjddsw';
        
        $items_in_warehouse_model->addItems($warehouse_id, $item_id, $lot_name, 2);
        $item_lot = $items_lot_model->getItemWithLotByItemId($item_id, $lot_name);
        $items_in_warehouse = $this->em->getRepository(WarehouseHasItem::class)->findBy([
            'warehouse_id' => $warehouse_id, 
            'item_with_lot_id' => $item_lot->getId()
        ]);
        Assert::count(2, $items_in_warehouse);
        
        Assert::exception(
            function() use($items_in_warehouse_model, $warehouse_id, $item_id, $lot_name) {
                $items_in_warehouse_model->addItems($warehouse_id, $item_id, $lot_name, 49);
            }, 
            WarehouseCapacityExceededException::class
        );
    }
    
    public function testNotUsedItem()
    {
        $data_creator = $this->data_creator_factory->create();
        $warehouse_id = $data_creator->createWarehouse('w_dflkldskdsaeewiejq');
        $item_id_1 = $data_creator->createItem('i_fffsdlkjddsw_1');
        $data_creator->createItem('i_fffsdlkjddsw_2');
        $this->items_in_warehouse_model_factory->create()->addItems($warehouse_id, $item_id_1, 'l_ddsadhjsae', 5);
        
        $items = $this->items_model_factory->create()->printList(false);
        $not_used_items = ArrayTools::searchInMultiArray($items, null, 'items_stored');
        $not_used_item_2 = ArrayTools::searchInMultiArray($not_used_items, 'i_fffsdlkjddsw_2', 'name');
        Assert::count(1, $not_used_item_2);
    }
    
    public function testFullList()
    {
        $data_creator = $this->data_creator_factory->create();
        $items_in_warehouse_full_model = $this->items_in_warehouse_full_model_factory->create();
        
        $data_creator->createTwoItemsInNewWarehouse('warehouse_nfjdsfjdhskds', 'item_nfjdsfjdhskds', false);
        $warehouse = $this->em->getRepository(Warehouse::class)->findOneByName('warehouse_nfjdsfjdhskds_1');
        
        $url = $this->http_request->getUrl()
                ->withQueryParameter('warehouse_id_cond', 'equal')
                ->withQueryParameter('warehouse_id_value', (string)$warehouse->getId())
                ->withQueryParameter('item_name_cond', 'like')
                ->withQueryParameter('item_name_value', 'item_nfjdsfjdhskds')
                ->withQueryParameter('sort_by', 'id')
                ->withQueryParameter('sort_desc', '1')
                ->withQueryParameter('page', '1')
                ;
        $http_request = new \Nette\Http\Request($url);
        $warehouse_full_list = $items_in_warehouse_full_model->getFullList($http_request);
        Assert::equal(10, $warehouse_full_list->getRowsCount());
    }
    
    protected function doBriefListTest(bool $reserve_few_items)
    {
        $data_creator = $this->data_creator_factory->create();
        $items_in_new_warehouse = $data_creator->createTwoItemsInNewWarehouse('warehouse_nfjdsfjdhskds', 'item_nfjdsfjdhskds', $reserve_few_items);
        $items_in_new_warehouse_list = $this->items_in_warehouse_model_factory->create()->getList($reserve_few_items);        
        Assert::equal($items_in_new_warehouse['warehouse_nfjdsfjdhskds_1'], $items_in_new_warehouse_list['warehouse_nfjdsfjdhskds_1']);
        Assert::equal($items_in_new_warehouse['warehouse_nfjdsfjdhskds_2'], $items_in_new_warehouse_list['warehouse_nfjdsfjdhskds_2']);
    }
    
}
