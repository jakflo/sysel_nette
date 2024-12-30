<?php

declare(strict_types=1);
namespace App\UI\ItemsInWarehouseFull;

use \Nette\Application\UI\Form;

class ItemsInWarehouseFullPresenter extends \Nette\Application\UI\Presenter 
{
    public function __construct(
            protected \App\UI\ItemsInWarehouseFull\ItemsInWarehouseFullModelFactory $items_in_warehouse_full_model_factory, 
            protected \App\UI\ItemsInWarehouseFull\ItemsInWarehouseFullFiltersFactory $items_in_warehouse_full_filters_factory
    )
    {
        
    }
    public function renderDefault()
    {
        $model = $this->items_in_warehouse_full_model_factory->create();
        $filters = $this->items_in_warehouse_full_filters_factory->create()->addItemToParamsForLatte();
        $pages = $model->getFullList($this->getHttpRequest());
        $this->template->fullList = $pages->getRows();
        $this->template->fullListFilters = $filters;
        $this->template->paginator = $pages;
    }
    
    protected function createComponentItemsListFilters(): Form {
        $form = new Form();
        $form->setMethod('GET');
        $this->items_in_warehouse_full_filters_factory->create()->addItemToFormComponents($form);
        $form->addSubmit('filter', 'Filtrovat');
        $form->onSuccess[] = [$this, 'itemsListFilters'];
        return $form;
    }
    
    public function itemsListFilters(Form $form, $data)
    {
        $this->items_in_warehouse_full_filters_factory->create()->addItemFormOnSubmit($form, $data);
    }
    
}
