<?php
namespace App\UI\Tests;

interface DataCreatorFactory
{
    public function create(): \App\UI\Tests\DataCreator;
}
