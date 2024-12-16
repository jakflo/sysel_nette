<?php
namespace App\UI\Tests;


class TestModelsFactory
{
    public function __construct(
            protected \App\UI\Tests\WarehouseListFactory $warehouse_list_factory, 
            protected \App\UI\Tests\ItemsListFactory $items_list_factory, 
            protected \App\UI\Tests\ItemsInWarehouseListFactory $items_in_warehouse_list_factory, 
            protected \App\UI\Tests\ItemsLotFactory $items_lot_factory
    )
    {
        
    }
    
    public function getWarehouseList(): \App\UI\Tests\WarehouseList
    {
        return $this->warehouse_list_factory->create();
    }
    
    public function getItemsList(): \App\UI\Tests\ItemsList
    {
        return $this->items_list_factory->create();
    }
    
    public function getItemsInWarehouseList(): \App\UI\Tests\ItemsInWarehouseList
    {
        return $this->items_in_warehouse_list_factory->create();
    }
    
    public function getItemsLot(): \App\UI\Tests\ItemsLot
    {
        return $this->items_lot_factory->create();
    }
    
}