<?php
namespace App\UI\OrderDetail;

class OrderDetailException extends \Exception
{
    const ORDERISNOTNEW = 1;
    const NOTALLITEMSFOUND = 2;
    const INVALIDSTATUSCHANGE = 3;
    const ORDERSITEMSMUSTBEASSIGNED = 4;
}
