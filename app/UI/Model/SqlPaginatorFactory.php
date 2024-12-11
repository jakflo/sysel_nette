<?php
namespace App\UI\Model;

use \Nette\Http\IRequest;

interface SqlPaginatorFactory
{
    /**     
     * @param IRequest $request
     * @param int $items_per_page
     * @param string $sql
     * @param array $sql_params
     * @param array $sql_params_types -nuti typ parametru, assoc. pole ve tvaru 'jmeno_sloupce' => objekt napr. \Doctrine\DBAL\Types\Type::getType('integer')
     * @return \App\UI\Model\SqlPaginator
     */
    public function create(IRequest $request, int $items_per_page, string $sql, array $sql_params, array $sql_params_types = []): \App\UI\Model\SqlPaginator;
}
