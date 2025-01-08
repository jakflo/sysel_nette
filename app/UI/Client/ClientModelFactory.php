<?php
namespace App\UI\Client;

interface ClientModelFactory
{
    public function create(): \App\UI\Client\ClientModel;
}
