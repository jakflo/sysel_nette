<?php
namespace App\UI\OrderDetail;

interface OrderDetailModelFactory
{
    public function create(int $order_id): \App\UI\OrderDetail\OrderDetailModel;
}
