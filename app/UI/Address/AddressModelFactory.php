<?php
namespace App\UI\Address;

interface AddressModelFactory
{
    public function create(): \App\UI\Address\AddressModel;
}
