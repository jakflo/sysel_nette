<?php
namespace tests\Classes;

use \Tester\Assert;
use \App\UI\Tools\ArrayTools;

class SearchItemsTestClass extends SyselTestCase
{
    public function __construct(
        protected \tests\Classes\DataCreatorFactory $data_creator_factory, 
        protected \Doctrine\ORM\EntityManager $em, 
        protected \App\UI\FindItems\ItemsQueryFactory $items_query_factory, 
        protected \App\UI\ItemsInWarehouse\ItemsInWarehouseModelFactory $items_in_warehouse_model_factory
    )
    {
        
    }
    
    public function testSelectWarehouses()
    {
        $data = $this->createScenario();
        $items_list = [
            [
                'item_id' => $data['items'][0], 
                'item_amount' => 2
            ], 
            [
                'item_id' => $data['items'][1], 
                'item_amount' => 2
            ]
        ];
        $items_query_1 = $this->items_query_factory->create(null, $items_list);
        $items_query_2 = $this->items_query_factory->create([$data['warehouses'][1]], $items_list);
        
        Assert::count(2, $items_query_1->getWarehousesWithAllItems());
        Assert::count(1, $items_query_2->getWarehousesWithAllItems());
        Assert::equal($data['warehouses'][1], array_keys($items_query_2->getWarehousesWithAllItems())[0]);
    }
    
    public function testWarehousesWithAllItems()
    {
        $data = $this->createScenario();
        $items_list = [
            [
                'item_id' => $data['items'][0], 
                'item_amount' => 8
            ], 
            [
                'item_id' => $data['items'][1], 
                'item_amount' => 8
            ]
        ];
        $items_query = $this->items_query_factory->create(null, $items_list);        
        
        Assert::equal($data['warehouses'][1], array_keys($items_query->getWarehousesWithAllItems())[0]);
    }
    
    public function testFindItemsInWarehouses()
    {
        $data = $this->createScenario();
        $items_list = [
            [
                'item_id' => $data['items'][0], 
                'item_amount' => 5
            ], 
            [
                'item_id' => $data['items'][1], 
                'item_amount' => 5
            ]
        ];
        $items_query = $this->items_query_factory->create(null, $items_list);        
        
        Assert::equal(5, $items_query->getItemAmount($data['items'][0], $data['warehouses'][0]));
        Assert::equal(5, $items_query->getItemAmount($data['items'][1], $data['warehouses'][0]));
        Assert::equal(10, $items_query->getItemAmount($data['items'][0], $data['warehouses'][1]));
        Assert::equal(10, $items_query->getItemAmount($data['items'][1], $data['warehouses'][1]));
    }
    
    public function testItemsNotFoundInWarehouses()
    {
        $data = $this->createScenario();
        $items_list = [
            [
                'item_id' => $data['items'][0], 
                'item_amount' => 17
            ], 
            [
                'item_id' => $data['items'][1], 
                'item_amount' => 20
            ]
        ];
        $items_query = $this->items_query_factory->create(null, $items_list);
        
        Assert::count(2, $items_query->getItemsNotFound());
        $item_1_not_found = ArrayTools::searchInMultiArray($items_query->getItemsNotFound(), $data['items'][0], 'item_id');
        Assert::count(1, $item_1_not_found);
        Assert::equal(2, $item_1_not_found[0]['item_amount']);
        $item_2_not_found = ArrayTools::searchInMultiArray($items_query->getItemsNotFound(), $data['items'][1], 'item_id');
        Assert::count(1, $item_2_not_found);
        Assert::equal(5, $item_2_not_found[0]['item_amount']);
    }
    
    protected function createScenario(): array
    {
        $data_creator = $this->data_creator_factory->create();
        $items_in_warehouse_model = $this->items_in_warehouse_model_factory->create();
        $warehouses = [];
        $items = [];
        
        $warehouse_1_id = $data_creator->createWarehouse('w_dleiufnnsybdsa_1');
        $warehouses[] = $warehouse_1_id;
        $warehouse_2_id = $data_creator->createWarehouse('w_dleiufnnsybdsa_2');
        $warehouses[] = $warehouse_2_id;
        
        $item_1_id = $data_creator->createItem('i_dleiufnnsybdsa_1');
        $items[] = $item_1_id;
        $item_2_id = $data_creator->createItem('i_dleiufnnsybdsa_2');
        $items[] = $item_2_id;
        
        $items_in_warehouse_model->addItems($warehouse_1_id, $item_1_id, 'l_dleiufnnsybdsa_1', 5);
        $items_in_warehouse_model->addItems($warehouse_1_id, $item_2_id, 'l_dleiufnnsybdsa_2', 5);
        $items_in_warehouse_model->addItems($warehouse_2_id, $item_1_id, 'l_dleiufnnsybdsa_1', 15);
        $items_in_warehouse_model->addItems($warehouse_2_id, $item_2_id, 'l_dleiufnnsybdsa_2', 15);
        $data_creator->reserveItems($warehouse_2_id, $item_1_id, 5);
        $data_creator->reserveItems($warehouse_2_id, $item_2_id, 5);
        
        return [
            'warehouses' => $warehouses, 
            'items' => $items
        ];
    }
    
}
