<?php
namespace App\UI\ItemsInWarehouseFull;

use \App\UI\Model\DqlPaginator;
use \Nette\Http\IRequest;

class ItemsInWarehouseFullModel
{
    public function __construct(
            protected \Doctrine\ORM\EntityManager $em, 
            protected \App\UI\ItemsInWarehouseFull\ItemsInWarehouseFullFiltersFactory $items_in_warehouse_full_filters_factory, 
            protected \App\UI\Model\SqlPaginatorFactory $sql_paginator_factory
    )
    {
        
    }
    
    public function getFullList(IRequest $request)
    {
        $filters = $this->items_in_warehouse_full_filters_factory->create();
        
        $adapter = new \App\UI\TableFilters\QueryBuilderToSqlAdapter();
        $filters->applyFilters($adapter, $request);
        if ($request->getQuery('sort_by') != 'wi.id') {
            $adapter->addOrderBy('wi.id', 'ASC');
        }
        
        $sql = "select wi.id, w.id AS warehouse_id, w.name AS warehouse, it.name AS item, its.name AS status 
                from warehouse_has_item wi 
                join warehouse w ON wi.warehouse_id = w.id 
                join item_with_lot il ON wi.item_with_lot_id = il.id 
                join item it ON il.item_id = it.id 
                join item_status its ON wi.status_id = its.id {$adapter->getWhereTerm(true)} {$adapter->getOrderByTerm(true)}";
                
        $list_page = $this->sql_paginator_factory->create($sql, $adapter->getParameters(), 15, $request);
        return $list_page;
                
        
        
//        $list = $this->em->createQueryBuilder()
//                ->select("wi.id, w.id AS warehouse_id, w.name AS warehouse, it.name AS item, its.name AS status")
//                ->from("App\\UI\\Entities\\WarehouseHasItem", 'wi')
//                ->join('wi.warehouse', 'w')                
//                ->join('wi.item_with_lot', 'il')
//                ->join('il.item', 'it')
//                ->join('wi.status', 'its')
//                ;
//        $filters->applyFilters($list, $request);
//        
//        if ($request->getQuery('sort_by') != 'wi.id') {
//            $list->addOrderBy('wi.id', 'ASC');
//        }
//        
//        $list_page = new DqlPaginator($list, 15, $request);
//        return $list_page;
    }
    
}
