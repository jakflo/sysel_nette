<?php
namespace App\UI\Model;

use \Nette\Http\IRequest;
use \Nette\Database\ResultSet;

class SqlPaginator extends PaginatorBase
{
    protected ResultSet $query;

    public function __construct(
            protected \App\UI\Model\Database $db,
            protected string $sql, 
            protected array $sql_params, 
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
        
        $this->rows_count = $this->db->query("SELECT COUNT(*) FROM ({$this->sql}) AS t", $this->sql_params)->fetchField();
        $this->sql_params[':limitcountqqq'] = $this->items_per_page;
        $this->sql_params[':limitoffsetqqq'] = $offset;
        $this->query = $this->db->query("{$this->sql} LIMIT :limitcountqqq OFFSET :limitoffsetqqq", $this->sql_params);
        $this->pages_count = ceil($this->rows_count / $this->items_per_page);
    }
    
    public function getRows(): \ArrayIterator
    {
        return new \ArrayIterator($this->query->fetchAll());
    }
    
}
