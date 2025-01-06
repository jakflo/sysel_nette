<?php
namespace App\UI\Orders;

interface OrdersModelFactory
{
    public function create(): \App\UI\Orders\OrdersModel;
}
