<?php
namespace App\UI\Exceptions;

class NotFoundException extends \Exception
{
    const WAREHOUSE = 1;
    const ITEM = 2;
    const MANUFACTURER = 3;
    const ITEMWITHLOT = 4;
    const ADDRESS = 5;
    const ORDER = 6;
}
