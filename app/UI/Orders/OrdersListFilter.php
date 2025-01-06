<?php
namespace App\UI\Orders;

use \App\UI\TableFilters\TableFilterCollection;
use \App\UI\TableFilters\TableFilterText;
use \App\UI\TableFilters\TableFilterSortOnly;
use \App\UI\TableFilters\TableFilterSelect;
use \App\UI\TableFilters\TableFilterDate;

class OrdersListFilter extends TableFilterCollection
{
    public function __construct(
            protected \App\UI\Orders\OrdersModelFactory $orders_model_factory
    )
    {
        $this->setFilters();
    }
    
    public function setFilters() 
    {
        $statuses = $this->orders_model_factory->create()->getOrderStatusesList();
        $this
                ->addFilter(new TableFilterSortOnly('id', '#', 'o.id'))
                ->addFilter(new TableFilterDate('added', 'Přidáno', 'o.added', true))
                ->addFilter(new TableFilterDate('last_edited', 'Změněno', 'o.last_edited', true))
                ->addFilter(new TableFilterText('note', 'Poznámka', 'o.note', true))
                ->addFilter(new TableFilterText('client_name', 'Jméno klienta', "CONCAT(c.forname, ' ', c.surname)", false))
                ->addFilter(new TableFilterSelect('status_name', 'Stav', 'st.id', $statuses, false))
                ;
    }
}
