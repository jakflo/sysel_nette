<?php
namespace App\UI\AddressList;

interface AddressModelFactory
{
    public function create(): \App\UI\AddressList\AddressModel;
}
