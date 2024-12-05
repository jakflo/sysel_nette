<?php
namespace App\UI\Model;

abstract class PaginatorBase
{
    protected int $rows_count;
    protected int $pages_count;
    protected int $current_page;
    
    protected abstract function setPaginator();
    
    public abstract function getRows(): \ArrayIterator;
        
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
