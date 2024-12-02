<?php
namespace App\UI\Model;

use \Doctrine\ORM\Tools\Pagination\Paginator AS PaginatorOrm;
use \Doctrine\ORM\QueryBuilder;
use \Doctrine\ORM\Query;
use \Nette\Http\IRequest;

class Paginator
{
    protected PaginatorOrm $paginator;
    protected int $rows_count;
    protected int $pages_count;
    protected int $current_page;

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
    
    public function getPagesCount(): int
    {
        return $this->pages_count;
    }
    
    public function getRowsCount(): int
    {
        return $this->rows_count;
    }
    
    public function getItemsPerPage(): int
    {
        return $this->items_per_page;
    }
    
    public function getCurrentPage(): int
    {
        return $this->current_page;
    }
    
}
