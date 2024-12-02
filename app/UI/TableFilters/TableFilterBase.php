<?php
namespace App\UI\TableFilters;

use \Nette\Application\UI\Form;
use \Doctrine\ORM\QueryBuilder;
use \Doctrine\ORM\Query;
use \Nette\Http\IRequest;

abstract class TableFilterBase
{
    protected string|null $forced_order_by_tableDotColunbName = null;

    abstract public function addItemToFormComponent(Form $form): Form;
    abstract public function addItemToParamsForLatte(array $params): array;
    abstract protected function printContitions(): array;
    abstract protected function addWhere(Query|QueryBuilder $query, string $condition, string $value);
    
    public function applyFilter(Query|QueryBuilder $query, IRequest $request)
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
    
    /**
     * pokud se ma polozka tridit dle jineho DB sloupce, nez filtrovat (napr. u select se filtruje dle id, ale tridi dle name)
     * @param string $new_tableDotColunbName DB tabulka.sloupec napr. user.name
     * @return $this
     */
    public function setForcedOrderByTableDotColunbName(string $new_tableDotColunbName)
    {
        $this->forced_order_by_tableDotColunbName = $new_tableDotColunbName;
        return $this;
    }
    
    protected function addOrderBy(Query|QueryBuilder $query, string $order_by, string $order_direction)
    {
        $order_by_tableDotColunbName = $this->forced_order_by_tableDotColunbName ?? $this->tableDotColunbName;
        if ($order_by == $this->name) {
            $query->orderBy($order_by_tableDotColunbName, $order_direction);
        }
    }
    
}
