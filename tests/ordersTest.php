<?php

require_once 'bootstrap.php';
use tests\Classes\OrdersTestClass;

createContainer()->getByType(OrdersTestClass::class)->run();
