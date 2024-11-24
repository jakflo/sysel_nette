<?php
namespace App\UI\ManufacturerList;

interface ManufacturerModelFactory
{
    public function create(): \App\UI\ManufacturerList\ManufacturerModel;
}
