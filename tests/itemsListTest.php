<?php

require_once 'bootstrap.php';
use tests\Classes\ItemsListTestClass;

createContainer()->getByType(ItemsListTestClass::class)->run();
