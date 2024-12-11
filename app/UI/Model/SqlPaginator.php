<?php
namespace App\UI\Model;

use \Nette\Http\IRequest;
use \Doctrine\DBAL\Types\Type;

class SqlPaginator extends PaginatorBase
{
    protected Array $rows;
    
    public function __construct(
            protected \App\UI\Model\Database $db,
            protected string $sql, 
            protected array $sql_params, 
            protected int $items_per_page, 
            protected IRequest $request, 
            protected array $sql_params_types
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
        
        $this->rows_count = $this->db->fetchOneField("SELECT COUNT(*) FROM ({$this->sql}) AS t", $this->sql_params);
        $this->sql_params['limitcountqqq'] = $this->items_per_page;
        $this->sql_params['limitoffsetqqq'] = $offset;
        $page_params_types = ['limitcountqqq' => Type::getType('integer'), 'limitoffsetqqq' => Type::getType('integer')];
        $this->rows = $this->db->fetchAllObjects(
                "{$this->sql} LIMIT :limitcountqqq OFFSET :limitoffsetqqq", 
                $this->sql_params, 
                array_merge($this->sql_params_types, $page_params_types)
        );
        $this->pages_count = ceil($this->rows_count / $this->items_per_page);
    }
    
    public function getRows(): \ArrayIterator
    {
        return new \ArrayIterator($this->rows);
    }
    
}
