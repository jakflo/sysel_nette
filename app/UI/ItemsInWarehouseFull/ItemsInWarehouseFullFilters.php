<?php
namespace App\UI\ItemsInWarehouseFull;

use \App\UI\TableFilters\TableFilterCollection;
use \App\UI\TableFilters\TableFilterText;
use \App\UI\TableFilters\TableFilterSortOnly;
use \App\UI\TableFilters\TableFilterSelect;
use \App\UI\TableFilters\TableFilterDate;

class ItemsInWarehouseFullFilters extends TableFilterCollection
{
    public function __construct(
            protected \App\UI\WarehouseList\WarehouseModelFactory $warehouse_model_factory, 
            protected \App\UI\ItemsList\ItemsModelFactory $items_model_factory
    )
    {
        $this->setFilters();
    }
    
    public function setFilters() 
    {
        $warehouses = $this->warehouse_model_factory->create()->printSimpleListForSelect();
        $statuses = $this->items_model_factory->create()->printItemStateList();
        $this
                ->addFilter(new TableFilterSortOnly('id', '#', 'wi.id'))
                ->addFilter((new TableFilterSelect('warehouse_id', 'Sklad', 'w.id', $warehouses, true))->setForcedOrderByTableDotColumnName('w.name'))
                ->addFilter(new TableFilterText('item_name', 'Položka', 'it.name', true))
                ->addFilter(new TableFilterDate('added_date', 'Přidáno', 'wi.added', true))
                ->addFilter(new TableFilterSelect('status_name', 'Stav', 'its.id', $statuses, false))
                ;
    }
}
