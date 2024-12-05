<?php
namespace App\UI\Model;

use \Doctrine\ORM\Tools\Pagination\Paginator AS PaginatorOrm;
use \Doctrine\ORM\QueryBuilder;
use \Doctrine\ORM\Query;
use \Nette\Http\IRequest;

class DqlPaginator extends PaginatorBase
{
    protected PaginatorOrm $paginator;

    public function __construct(
            protected QueryBuilder|Query $query, 
            protected int $items_per_page, 
            protected IRequest $request
    )
    {
        $this->setPaginator();
    }
    
    protected function setPaginator()
    {
        $get_params = $this->request->getQuery();
        $this->current_page = $get_params['page'] ?? 1;
        $_offset = ($this->current_page - 1) * $this->items_per_page;
        $offset = $_offset < 0 ? 0 : $_offset;
        
        $this->query->setFirstResult($offset)->setMaxResults($this->items_per_page);
        $this->paginator = new PaginatorOrm($this->query);
        $this->paginator->setUseOutputWalkers(false);
        $this->rows_count = $this->paginator->count();
        $this->pages_count = ceil($this->rows_count / $this->items_per_page);
    }
    
    public function getRows(): \ArrayIterator
    {
        return $this->paginator->getIterator();
    }
    
}
