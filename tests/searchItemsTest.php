<?php

require_once 'bootstrap.php';
use tests\Classes\SearchItemsTestClass;

createContainer()->getByType(SearchItemsTestClass::class)->run();
