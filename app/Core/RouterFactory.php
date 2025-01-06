<?php

declare(strict_types=1);

namespace App\Core;

use Nette;
use Nette\Application\Routers\RouteList;


final class RouterFactory
{
	use Nette\StaticClass;

	public static function createRouter(): RouteList
	{
		$router = new RouteList;
                $router->addRoute('items-in-warehouse/<mode=all>', 'ItemsInWarehouse:default');
                $router->addRoute('items-lot-list/<id>', 'ItemsLotList:default');
                $router->addRoute('order-detail/<id>', 'OrderDetail:default');
                $router->addRoute('order-assign/<id>', 'OrderDetail:assignItems');
		$router->addRoute('<presenter>/<action>[/<id>]', 'Home:default');
		return $router;
	}
}
