<?php
namespace App\UI\OrderDetail;

class OrderDetailException extends \Exception
{
    const ORDERISNOTNEW = 1;
    const NOTALLITEMSFOUND = 2;
}
