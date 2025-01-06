<?php
namespace App\UI\Orders;

interface OrdersListFilterFactory
{
    public function create(): \App\UI\Orders\OrdersListFilter;
}
