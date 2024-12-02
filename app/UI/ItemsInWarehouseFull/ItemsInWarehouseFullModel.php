<?php
namespace App\UI\ItemsInWarehouseFull;

use \App\UI\Model\Paginator;
use \Nette\Http\IRequest;

class ItemsInWarehouseFullModel
{
    public function __construct(
            protected \Doctrine\ORM\EntityManager $em, 
            protected \App\UI\ItemsInWarehouseFull\ItemsInWarehouseFullFiltersFactory $items_in_warehouse_full_filters_factory
    )
    {
        
    }
    
    public function getFullList(IRequest $request)
    {
        $filters = $this->items_in_warehouse_full_filters_factory->create();        
        $list = $this->em->createQueryBuilder()
                ->select("wi.id, w.id AS warehouse_id, w.name AS warehouse, it.name AS item, its.name AS status")
                ->from("App\\UI\\Entities\\WarehouseHasItem", 'wi')
                ->join('wi.warehouse', 'w')                
                ->join('wi.item_with_lot', 'il')
                ->join('il.item', 'it')
                ->join('wi.status', 'its')
                ;
        $filters->applyFilters($list, $request);
        
        if ($request->getQuery('sort_by') != 'wi.id') {
            $list->addOrderBy('wi.id', 'ASC');
        }
        
        $list_page = new Paginator($list, 15, $request);
        return $list_page;
    }
    
}
