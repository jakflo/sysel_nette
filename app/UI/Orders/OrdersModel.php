<?php
namespace App\UI\Orders;

use \Nette\Http\IRequest;
use \App\UI\Model\DqlPaginator;

class OrdersModel
{
    public function __construct(
            protected \App\UI\Model\Database $db, 
            protected \Doctrine\ORM\EntityManager $em, 
            protected \App\UI\Model\SqlPaginatorFactory $sql_paginator_factory, 
            protected \App\UI\Orders\OrdersListFilterFactory $orders_list_filter_factory
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
    
    public function getOrderStatusesList()
    {
        return $this->db->fetchPairs("SELECT id, name FROM order_status ORDER BY id");
    }
    
}
