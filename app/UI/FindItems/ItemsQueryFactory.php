<?php
namespace App\UI\FindItems;

interface ItemsQueryFactory
{
    public function create(array|null $used_warehouses_id, array $items_list): \App\UI\FindItems\ItemsQuery;
}
