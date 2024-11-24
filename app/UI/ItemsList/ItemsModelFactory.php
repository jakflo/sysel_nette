<?php
namespace App\UI\ItemsList;

interface ItemsModelFactory
{
    public function create(): \App\UI\ItemsList\ItemsModel;
}
