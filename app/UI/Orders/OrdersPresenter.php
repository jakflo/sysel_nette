<?php

declare(strict_types=1);

namespace App\UI\Orders;

use \Nette\Application\UI\Form;

final class OrdersPresenter extends \Nette\Application\UI\Presenter
{
    public function __construct(
            protected \App\UI\Orders\OrdersModelFactory $orders_model_factory, 
            protected \App\UI\Orders\OrdersListFilterFactory $orders_list_filter_factory
    )
    {
        
    }
    
    public function renderDefault()
    {
        $model = $this->orders_model_factory->create();
        $filters = $this->getOrdersListFilters()->addItemToParamsForLatte();
        $pages = $model->getOrdersList($this->getHttpRequest());
        
        $this->template->fullList = $pages->getRows();
        $this->template->fullListFilters = $filters;
        $this->template->paginator = $pages;
        $this->template->title = 'Syslovo sklad | Objednávky';
    }
    
    protected function createComponentItemsListFilters(): Form {
        $form = new Form();
        $form->setMethod('GET');
        $this->getOrdersListFilters()->addItemToFormComponents($form);
        $form->addSubmit('filter', 'Filtrovat');
        $form->onSuccess[] = [$this, 'ordersListFilters'];
        return $form;
    }
    
    public function ordersListFilters(Form $form, $data)
    {
        $this->getOrdersListFilters()->addItemFormOnSubmit($form, $data);
    }
    
    protected function getOrdersListFilters(): \App\UI\Orders\OrdersListFilter
    {
        static $filters = null;
        if ($filters === null) {
            $filters = $this->orders_list_filter_factory->create();
        }
        return $filters;
    }
    
}
