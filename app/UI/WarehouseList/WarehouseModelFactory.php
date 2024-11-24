<?php

namespace App\UI\WarehouseList;

interface WarehouseModelFactory
{
    public function create(): \App\UI\WarehouseList\WarehouseModel;
}
