<?php

declare(strict_types=1);
namespace App\UI\FindItems;

use \App\UI\Tools\ArrayTools;
use \App\UI\FindItems\QueryException;
use \App\UI\Exceptions\NotFoundException;

final class FindItemsPresenter extends \Nette\Application\UI\Presenter
{
    public function __construct(
            protected \App\UI\ItemsList\ItemsModelFactory $items_model_factory, 
            protected \App\UI\WarehouseList\WarehouseModelFactory $warehouse_model_factory, 
            protected \App\UI\FindItems\FindItemsModelFactory $find_items_model_factory, 
            protected \App\UI\FindItems\ItemsQueryFactory $items_query_factory
    )
    {
        
    }
    
    public function renderDefault()
    {
        $items_model = $this->items_model_factory->create();        
        $query_data = $this->getQueryData();
        
        if ($query_data) {
            try {
                $this->validateQuery($query_data);
                $this->fixVarTypesInQuery($query_data);
            } catch (QueryException $e) {
                $this->flashMessage('Chybný dotaz', 'error');
                $this->redirect('FindItems:default');
            }
            
            $query_data['items'] = $this->sumItemsInQuery($query_data['items']);
            $default_values = $query_data;
            $default_values['items'] = array_values($default_values['items']);
            
            $warehouses_id = $query_data['warehouses']['select_all'] == 0 ? $query_data['warehouses']['selected'] : null;
            $items_requested = $query_data['items'];
            $items_query_model = $this->items_query_factory->create($warehouses_id, $items_requested);
            $this->template->items_requested = $items_requested;
            $this->template->items_query = $items_query_model;
        } else {
            $default_values = [
                'warehouses' => $this->getDefaultWarehousesSelection(), 
                'items' => []
            ];
            
            $this->template->items_requested = null;
            $this->template->items_query = null;
        }
        
        
        $warehouse_model = $this->warehouse_model_factory->create();
        
        
        $data_for_react = [
            'warehouses' => $warehouse_model->printSimpleListForSelect(), 
            'items' => $items_model->printSimpleList(), 
            'values' => $default_values
        ];
        
        $this->template->title = 'Syslovo sklad | Hledat položky';
        $this->template->data_for_react = base64_encode(json_encode($data_for_react));
        
    }
    
    protected function getQueryData(): array|null
    {
        $query = $this->getHttpRequest()->getQuery();
        
        if (!isset($query['query'])) {
            return null;
        }
        
        $query_data = json_decode($query['query'], true);
        if (count($query_data['items']) == 1) {
            $item = reset($query_data['items']);
            if ($item['item_id'] == 0 || empty($item['item_amount'])) {
                return null;
            }
        }
        
        return $query_data;
    }
    
    protected function validateQuery(array $query)
    {
        $find_items_model = $this->find_items_model_factory->create();
        $warehouses = $query['warehouses'];
        $items_id = array_column($query['items'], 'item_id');
        $items_amount = array_column($query['items'], 'item_amount');
        
        if ($warehouses['select_all'] != 1 && count($warehouses['selected']) == 0) {
            throw new QueryException('nevybran sklad');
        }
        
        try {
            $find_items_model->checkWarehousesExist($warehouses['selected']);
            $find_items_model->checkItemsExist($items_id);
        } catch (NotFoundException $e) {
            throw new QueryException('nenalezen sklad nebo polozka');
        }
        
        foreach ($items_amount as $value) {
            if (!is_numeric($value) || (int)$value != $value || $value < 1) {
                throw new QueryException('neplatna hodnota mnozstvi polozky');
            }
        }
    }
    
    protected function fixVarTypesInQuery(array &$query_data)
    {
        $query_data['warehouses']['select_all'] = (int)$query_data['warehouses']['select_all'];
        foreach ($query_data['warehouses']['selected'] as &$warehouse_id) {
            $warehouse_id = (int)$warehouse_id;
        }
        
        foreach ($query_data['items'] as &$item) {
            $item['item_id'] = (int)$item['item_id'];
            $item['item_amount'] = (int)$item['item_amount'];
        }
    }
    
    // je-li v query vice polozek jednoho druhu, tak je poscita
    protected function sumItemsInQuery(array $items): array
    {
        $items_id = array_unique(array_column($items, 'item_id'));
        $result = [];
        
        foreach ($items_id as $item_id) {
            $items_with_id = ArrayTools::searchInMultiArray($items, $item_id, 'item_id');
            $item_amount = array_sum(array_column($items_with_id, 'item_amount'));
            $result[] = [
                'item_id' => $item_id, 
                'item_amount' => $item_amount
            ];
        }
        
        return $result;
    }
    
    protected function getDefaultWarehousesSelection()
    {
        $warehouse_model = $this->warehouse_model_factory->create();
        $warehouse_ids = array_keys($warehouse_model->printSimpleListForSelect());
        return [
            'select_all' => true, 
            'selected' => $warehouse_ids
        ];
    }
}
