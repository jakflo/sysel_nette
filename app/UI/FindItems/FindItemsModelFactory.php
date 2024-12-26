<?php
namespace App\UI\FindItems;

interface FindItemsModelFactory
{
    public function create(): \App\UI\FindItems\FindItemsModel;
}
