<?php
namespace tests\Classes;

use \App\UI\Entities\Orders;

class OrdersScenario
{
    protected int $warehouse_1_id;
    protected int $warehouse_2_id;
    protected int $warehouse_3_id;
    protected int $item_1_id;
    protected int $item_2_id;
    protected int $item_3_id;
    protected array $order_items;
    protected Orders $order_1;
    protected Orders $order_2;
    protected Orders $order_3;
    
    public function __construct(
        protected \tests\Classes\DataCreatorFactory $data_creator_factory,         
        protected \App\UI\Orders\OrdersModelFactory $orders_model_factory,          
        protected \App\UI\ItemsInWarehouse\ItemsInWarehouseModelFactory $items_in_warehouse_model_factory
    )
    {
        $this->create();
    }
    
    protected function create()
    {
        $orders_model = $this->orders_model_factory->create();
        $data_creator = $this->data_creator_factory->create();
        $items_in_warehouse_model = $this->items_in_warehouse_model_factory->create();
        
        $this->warehouse_1_id = $data_creator->createWarehouse('w_ddeerfdss_1');
        $this->warehouse_2_id = $data_creator->createWarehouse('w_ddeerfdss_2');
        $this->warehouse_3_id = $data_creator->createWarehouse('w_ddeerfdss_3', 31);
        
        $this->item_1_id = $data_creator->createItem('i_ddeerfdss_1');
        $this->item_2_id = $data_creator->createItem('i_ddeerfdss_2');
        $this->item_3_id = $data_creator->createItem('i_ddeerfdss_3');
        
        $items_in_warehouse_model->addItems($this->warehouse_1_id, $this->item_1_id, 'l_ddeerfdss_1', 5);
        $items_in_warehouse_model->addItems($this->warehouse_1_id, $this->item_2_id, 'l_ddeerfdss_2', 5);
        $items_in_warehouse_model->addItems($this->warehouse_2_id, $this->item_1_id, 'l_ddeerfdss_1', 5);
        $items_in_warehouse_model->addItems($this->warehouse_2_id, $this->item_2_id, 'l_ddeerfdss_2', 5);
        $items_in_warehouse_model->addItems($this->warehouse_3_id, $this->item_1_id, 'l_ddeerfdss_1', 5);
        $items_in_warehouse_model->addItems($this->warehouse_3_id, $this->item_2_id, 'l_ddeerfdss_2', 5);
        
        $items_in_warehouse_model->addItems($this->warehouse_3_id, $this->item_3_id, '3_ddeerfdss_3', 5);
        $items_in_warehouse_model->addItems($this->warehouse_1_id, $this->item_3_id, 'l_ddeerfdss_3', 5);
        $items_in_warehouse_model->addItems($this->warehouse_2_id, $this->item_3_id, 'l_ddeerfdss_3', 5);
        
        $order_items = [
            [
                'item_id' => $this->item_1_id, 
                'item_amount' => 7
            ], 
            [
                'item_id' => $this->item_2_id, 
                'item_amount' => 8
            ]
        ];
        
        $order3_items = [
            [
                'item_id' => $this->item_3_id,
                'item_amount' => 8
            ]
        ];
        
        $this->order_1 = $orders_model->create_order_with_items($data_creator->createClient('dsjhkdh_1'), $order_items, 'neco_rewiuiiofsdjje');
        $this->order_2 = $orders_model->create_order_with_items($data_creator->createClient('dsjhkdh_2'), $order_items, null);
        $this->order_3 = $orders_model->create_order_with_items($data_creator->createClient('dsjhkdh_3'), $order3_items, null);
    }
    
    public function getWarehouse1Id(): int
    {
        return $this->warehouse_1_id;
    }
    
    public function getWarehouse2Id(): int
    {
        return $this->warehouse_2_id;
    }
    
    public function getWarehouse3Id(): int
    {
        return $this->warehouse_3_id;
    }
    
    public function getItem1Id(): int
    {
        return $this->item_1_id;
    }
    
    public function getItem2Id(): int
    {
        return $this->item_2_id;
    }
    
    public function getItem3Id(): int
    {
        return $this->item_3_id;
    }
    
    public function getOrderItems(): array
    {
        return $this->order_items;
    }
    
    public function getOrder1(): Orders
    {
        return $this->order_1;
    }
    
    public function getOrder2(): Orders
    {
        return $this->order_2;
    }
    
    public function getOrder3(): Orders
    {
        return $this->order_3;
    }
    
}
