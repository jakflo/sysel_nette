<?php
namespace tests\Classes;

interface DataCreatorFactory
{
    public function create(): \tests\Classes\DataCreator;
}
