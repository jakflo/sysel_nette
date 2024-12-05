<?php
namespace App\UI\TableFilters;

use \Nette\Application\UI\Form;
use \Doctrine\ORM\QueryBuilder;
use \Doctrine\ORM\Query;
use \App\UI\TableFilters\QueryBuilderToSqlAdapter;
use \Nette\Http\IRequest;

abstract class TableFilterBase
{
    protected string|null $forced_order_by_tableDotColumnName = null;

    abstract public function addItemToFormComponent(Form $form): Form;
    abstract public function addItemToParamsForLatte(array $params): array;
    abstract protected function printContitions(): array;
    abstract protected function addWhere(Query|QueryBuilder|QueryBuilderToSqlAdapter $query, string $condition, string $value);
    
    public function applyFilter(Query|QueryBuilder|QueryBuilderToSqlAdapter $query, IRequest $request)
    {
        $condition = $request->getQuery($this->name . '_cond');
        $value = $request->getQuery($this->name . '_value');
        $order_by = $request->getQuery('sort_by');
        $order_direction = $request->getQuery('sort_desc') == 1 ? 'DESC' : 'ASC';
        
        if ($value) {
            $this->addWhere($query, $condition, $value);
        }
        if ($this->sortable && $order_by) {
            $this->addOrderBy($query, $order_by, $order_direction);
        }
    }
    
    // potrebuji rozlisit jmena parametru, tak k nim pridam jmeno filtrovaneho hodnoty, ale radsi ne naprimo, ale prvnich 6 znaku md5
    public function printHashedName(string $name): string
    {
        return substr(md5($name), 0, 6);
    }
    
    /**
     * pokud se ma polozka tridit dle jineho DB sloupce, nez filtrovat (napr. u select se filtruje dle id, ale tridi dle name)
     * @param string $new_tableDotColumnName DB tabulka.sloupec napr. user.name
     * @return $this
     */
    public function setForcedOrderByTableDotColumnName(string $new_tableDotColumnName)
    {
        $this->forced_order_by_tableDotColumnName = $new_tableDotColumnName;
        return $this;
    }
    
    protected function addOrderBy(Query|QueryBuilder|QueryBuilderToSqlAdapter $query, string $order_by, string $order_direction)
    {
        if (!in_array($order_direction, ['ASC', 'DESC'])) {
            throw new \Exception('pripustne pouze ASC a DESC');
        }
        
        $order_by_tableDotColumnName = $this->forced_order_by_tableDotColumnName ?? $this->tableDotColumnName;
        if ($order_by == $this->name) {
            $query->orderBy($order_by_tableDotColumnName, $order_direction);
        }
    }
    
}
