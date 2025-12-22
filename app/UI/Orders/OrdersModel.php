<?php
namespace App\UI\Orders;

use \Nette\Http\IRequest;
use \App\UI\Model\DqlPaginator;
use \App\UI\Entities\Orders;
use \App\UI\Entities\OrderHasItem;
use \App\UI\Entities\OrderStatus;
use \App\UI\Exceptions\NotFoundException;
use \App\UI\Tools\ArrayTools;

class OrdersModel
{
    public function __construct(
            protected \Doctrine\ORM\EntityManager $em, 
            protected \App\UI\Model\SqlPaginatorFactory $sql_paginator_factory, 
            protected \App\UI\Orders\OrdersListFilterFactory $orders_list_filter_factory, 
            protected \App\UI\Client\ClientModelFactory $client_model_factory, 
            protected \App\UI\ItemsList\ItemsModelFactory $items_model_factory
    )
    {
        
    }
    
    public function getOrdersList(IRequest $request)
    {
        $filters = $this->orders_list_filter_factory->create();
        
        $list = $this->em->createQueryBuilder()
                ->select("o.id, o.added, o.last_edited, o.note, c.forname, c.surname, st.name AS status, st.id AS status_id, st.short_name AS status_shortname")
                ->from("App\\UI\\Entities\\Orders", 'o')
                ->join('o.client', 'c')                
                ->join('o.status', 'st')                
                ;
        
        $filters->applyFilters($list, $request);
        
        if ($request->getQuery('sort_by') != 'o.added' && !$request->getQuery('sort_desc')) {
            $list->addOrderBy('o.added', 'DESC');
        }
        
        $list_page = new DqlPaginator($list, 15, $request);
        return $list_page;
    }
    
    public function getOrder(int $order_id): Orders
    {
        $order = $this->em->getRepository(Orders::class)->findOneById($order_id);
        if (!$order) {
            throw new NotFoundException('Objednavka nenalezena', NotFoundException::ORDER);
        }
        return $order;
    }
    
    /**
     * 
     * @param int $client_id
     * @param array $items ve formatu napr. [['item_id' => 2, 'item_amount' => 5], ['item_id' => 1, 'item_amount' => 10]]
     * @param string|null $note
     * @throws \Exception
     */
    public function create_order_with_items(int $client_id, array $items, string|null $note = null): Orders
    {
        if (count($items) == 0) {
            throw new \Exception('Seznam polozek nemuze byt prazdny');
        }
        
        $items_model = $this->items_model_factory->create();
        $client = $this->client_model_factory->create()->getClient($client_id);
        $status_new = $this->em->getRepository(OrderStatus::class)->findOneBy(['short_name' => 'new']);
        
        $order = new Orders();
        $order->setClient($client)
                ->setNote($note)
                ->setStatus($status_new)
                ->setAdded(new \DateTime())
                ;
        $this->em->persist($order);
        
        $order_has_item_list = [];
        foreach ($items as $item) {
            $item_entity = $items_model->getItem($item['item_id']);
            
            $order_has_item = new OrderHasItem();
            $order_has_item
                    ->setOrder($order)
                    ->setItem($item_entity)
                    ->setAmount($item['item_amount'])
                    ;
            $this->em->persist($order_has_item);
            $order_has_item_list[] = $order_has_item;
        }
        
        $this->em->flush();
        return $order;
    }
    
    public function getOrderStatusesList()
    {
        return ArrayTools::asocPairsForFirstTwoInMultiarray($this->em->getConnection()->fetchAllAssociative("SELECT id, name FROM order_status ORDER BY id"));
    }
    
}
