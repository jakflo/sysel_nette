<?php
namespace App\UI\Tests;

use \Tester\Assert;
use \App\UI\OrderDetail\OrderDetailModel;
use \App\UI\OrderDetail\OrderDetailException;
use \App\UI\Entities\WarehouseHasItem;
use \App\UI\Entities\ItemStatus;

class Orders extends \App\UI\Tests\TestCase
{
    public function __construct(
        protected \App\UI\Tests\DataCreatorFactory $data_creator_factory, 
        protected \App\UI\Tests\OrdersScenarioFactory $orders_scenario_factory, 
        protected \Doctrine\ORM\EntityManager $em, 
        protected \App\UI\Orders\OrdersModelFactory $orders_model_factory, 
        protected \App\UI\OrderDetail\OrderDetailModelFactory $order_detail_model_factory, 
        protected \Nette\Http\IRequest $http_request
    )
    {
        
    }
    
    public function testOrderList()
    {
        $orders_model = $this->orders_model_factory->create();
        $data_creator = $this->data_creator_factory->create();
        $order_items = [
            [
                'item_id' => $data_creator->createItem('dsaddsasda_1'), 
                'item_amount' => 5
            ], 
            [
                'item_id' => $data_creator->createItem('dsaddsasda_2'), 
                'item_amount' => 5
            ]
        ];
        
        $orders_model->create_order_with_items($data_creator->createClient('dsjhkdh_1'), $order_items, 'neco_rewiuiiofsdjje');
        $orders_model->create_order_with_items($data_creator->createClient('dsjhkdh_2'), $order_items, null);
        
        $url = $this->http_request->getUrl()
                ->withQueryParameter('note_cond', 'equal')
                ->withQueryParameter('note_value', 'neco_rewiuiiofsdjje')
                ;
        $http_request_with_note = new \Nette\Http\Request($url);
        
        $orders_list_1 = $orders_model->getOrdersList($this->http_request);
        $orders_list_2 = $orders_model->getOrdersList($http_request_with_note);

        Assert::true($orders_list_1->getRowsCount() > 0);
        Assert::equal($orders_list_2->getRowsCount(), 1);
    }
    
    public function testAssignItems()
    {
        $orders_scenario = $this->orders_scenario_factory->create();
        $warehouse_2_id = $orders_scenario->getWarehouse2Id();
        $order_detail_1 = $this->order_detail_model_factory->create($orders_scenario->getOrder1()->getId());
        $order_detail_2 = $this->order_detail_model_factory->create($orders_scenario->getOrder2()->getId());
        
        $order_detail_1->assignItemsToOrder([$orders_scenario->getWarehouse2Id()]);
        $order_1_id = $orders_scenario->getOrder1()->getId();
        Assert::equal($order_detail_1->getOrderDetails()['status_shortname'], 'items_reserved');
        
        Assert::equal($this->getReservedItemsInWarehouseCount($warehouse_2_id, $orders_scenario->getItem1Id(), $order_1_id), 5);
        Assert::equal($this->getReservedItemsInWarehouseCount($warehouse_2_id, $orders_scenario->getItem2Id(), $order_1_id), 5);
        Assert::equal($this->getReservedItemsInWarehouseCount($orders_scenario->getWarehouse3Id(), $orders_scenario->getItem1Id(), $order_1_id), 2);
        Assert::equal($this->getReservedItemsInWarehouseCount($orders_scenario->getWarehouse3Id(), $orders_scenario->getItem2Id(), $order_1_id), 3);
        
        Assert::exception(
            function() use($order_detail_1, $warehouse_2_id) {
                $order_detail_1->assignItemsToOrder([$warehouse_2_id]);
            }, 
            OrderDetailException::class, 
            null, 
            OrderDetailException::ORDERISNOTNEW
        );
        Assert::exception(
            function() use($order_detail_2, $warehouse_2_id) {
                $order_detail_2->assignItemsToOrder([$warehouse_2_id]);
            }, 
            OrderDetailException::class, 
            null, 
            OrderDetailException::NOTALLITEMSFOUND
        );
    }
    
    public function testChangeOrderState()
    {
        $orders_scenario = $this->orders_scenario_factory->create();
        $order_detail = $this->order_detail_model_factory->create($orders_scenario->getOrder1()->getId());
        
        Assert::exception(
            function() use($order_detail) {
                $order_detail->changeOrderStatus('items_reserved');
            }, 
            OrderDetailException::class, 
            null, 
            OrderDetailException::ORDERSITEMSMUSTBEASSIGNED
        );
            
        Assert::exception(
            function() use($order_detail) {
                $order_detail->changeOrderStatus('sent_off');
            }, 
            OrderDetailException::class, 
            null, 
            OrderDetailException::INVALIDSTATUSCHANGE
        );
            
            $order_detail->assignItemsToOrder();
            
            $this->checkOrderStatusChange($order_detail, 'sent_off', 'sent_off');
            $this->checkOrderStatusChange($order_detail, 'complain_in_progress', 'sent_off');
            $this->checkOrderStatusChange($order_detail, 'items_returned', 'returned');
            $this->checkOrderStatusChange($order_detail, 'complain_in_progress', 'sent_off');
            $this->checkOrderStatusChange($order_detail, 'sent_off', 'sent_off');
            $this->checkOrderStatusChange($order_detail, 'items_reserved', 'reserved');
            $this->checkOrderStatusChange($order_detail, 'storno', 'available');
            $this->checkOrderStatusChange($order_detail, 'new', 'available');
    }
    
    protected function getReservedItemsInWarehouseCount(int $warehouse_id, int $item_id, int $order_id): int
    {
        $items_in_warehouse = $this->em->createQuery(
            "SELECT wi 
            FROM App\\UI\\Entities\\WarehouseHasItem wi 
            JOIN wi.status its 
            JOIN wi.item_with_lot il 
            WHERE wi.warehouse_id = :wid 
                AND wi.order_id = :oid 
                AND il.item_id = :iid 
                AND its.short_name = 'reserved'"
        )
            ->setParameters([
                'wid' => $warehouse_id, 
                'oid' => $order_id, 
                'iid' => $item_id
            ])
            ->getResult()
            ;
        
        return count($items_in_warehouse);
    }
    
    protected function checkOrderStatusChange(OrderDetailModel $order_detail, string $new_order_status, string $expected_items_status)
    {
        $order_id = $order_detail->getOrderId();
        $order_items_count = array_sum(array_column($order_detail->getItemsInOrder(), 'item_amount'));
        $order_detail->changeOrderStatus($new_order_status);
        
        Assert::equal($order_detail->getOrderDetails()['status_shortname'], $new_order_status);
        
        if ($expected_items_status !== 'available') {
            $items_in_order_with_status = $this->em->createQuery(
                "SELECT wi 
                FROM App\\UI\\Entities\\WarehouseHasItem wi 
                JOIN wi.status its             
                WHERE wi.order_id = :oid                 
                    AND its.short_name = :its"
            )
                ->setParameters([                
                    'oid' => $order_id, 
                    'its' => $expected_items_status
                ])
                ->getResult()
            ;
            Assert::count($order_items_count, $items_in_order_with_status);
        } else {
            $items_in_order = $this->em->createQuery(
                "SELECT wi 
                FROM App\\UI\\Entities\\WarehouseHasItem wi             
                WHERE wi.order_id = :oid"
            )
                ->setParameters([                
                    'oid' => $order_id
                ])
                ->getResult()
            ;
            Assert::count(0, $items_in_order);
        }
        
    }
    
}
