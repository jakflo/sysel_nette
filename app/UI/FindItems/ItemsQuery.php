<?php
namespace App\UI\FindItems;

use \App\UI\Tools\ArrayTools;

class ItemsQuery
{
    protected array $warehouse_names;
    protected array $item_names;
    protected array $warehouses_with_all_items;
    protected array $list_by_items;
    protected array $items_not_found;

    public function __construct(
            protected \App\UI\ItemsInWarehouse\ItemsInWarehouseModelFactory $items_in_warehouse_model_factory, 
            protected \App\UI\WarehouseList\WarehouseModelFactory $warehouse_model_factory, 
            protected array|null $used_warehouses_id, 
            protected array $items_list
    )
    {
        $this->setWarehouseNames();
        $this->doQuery();
    }
    
    protected function doQuery()
    {
        $items_id = array_column($this->items_list, 'item_id');
        $items_in_warehouse_model = $this->items_in_warehouse_model_factory->create();
        $list_by_warehouses = $items_in_warehouse_model->getList(true, $this->used_warehouses_id, $items_id);
        $this->items_not_found = $this->items_list;
        
        $list = [];
        foreach ($list_by_warehouses as $items) {
            $list = array_merge($list, $items);
        }
        
        $this->list_by_items = ArrayTools::groupMultiArray($list, 'item_id');
        $this->item_names = ArrayTools::multiarrayToAsocPairs($list, 'item_id', 'item');
        $this->setWarehousesWithAllItems();
    }
    
    protected function setWarehousesWithAllItems()
    {
        $all_warehouses = $this->warehouse_names;
        foreach ($all_warehouses as $warehouse_id => $warehouse_name) {
            foreach ($this->items_list as $item) {
                $item_amount = $this->getItemAmount($item['item_id'], $warehouse_id);
                $this->substractItemsAmountFromNotFound($item['item_id'], $item_amount);
                if ($item_amount < $item['item_amount']) {
                    unset($all_warehouses[$warehouse_id]);
//                    break;
                }
            }
        }
        $this->warehouses_with_all_items = $all_warehouses;
    }
    
    protected function setWarehouseNames() 
    {
        $warehouse_model = $this->warehouse_model_factory->create();
        $warehouses_names = $warehouse_model->printSimpleListForSelect();
        
        if (!$this->used_warehouses_id) {
            $this->warehouse_names = $warehouses_names;
        } else {
            $result = [];
            foreach ($this->used_warehouses_id as $used_warehouse_id) {
                $result[$used_warehouse_id] = $warehouses_names[$used_warehouse_id];
            }
            $this->warehouse_names = $result;
        }
    }
    
    protected function substractItemsAmountFromNotFound(int $item_id, int $item_amount)
    {
        $items_not_found_key = array_keys(ArrayTools::searchInMultiArray($this->items_not_found, $item_id, 'item_id', true))[0];
        $this->items_not_found[$items_not_found_key]['item_amount'] -= $item_amount;
    }
    
    public function getItemAmount(int $item_id, int $warehouse_id): int
    {
        $item_amount = ArrayTools::searchInMultiArray($this->list_by_items[$item_id], $warehouse_id, 'warehouse_id');
        return count($item_amount) > 0 ? reset($item_amount)['n'] : 0;
    }
    
    public function getWarehouseNames(): array
    {
        return $this->warehouse_names;
    }
    
    public function getItemNames(): array
    {
        return $this->item_names;
    }
    
    public function getWarehousesWithAllItems(): array
    {
        return $this->warehouses_with_all_items;
    }
    
    public function getItemsNotFound(): array
    {
        return array_filter($this->items_not_found, function($row) {
            return $row['item_amount'] > 0;
        });
    }
}
