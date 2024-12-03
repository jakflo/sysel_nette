<?php
namespace App\UI\ItemsLotList;

interface ItemsLotModelFactory
{
    public function create(): \App\UI\ItemsLotList\ItemsLotModel;
}
