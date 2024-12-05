<?php
namespace App\UI\Model;

use \Nette\Http\IRequest;

interface SqlPaginatorFactory
{
    public function create(string $sql, array $sql_params, int $items_per_page, IRequest $request): \App\UI\Model\SqlPaginator;
}
