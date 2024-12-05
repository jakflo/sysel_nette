<?php
namespace App\UI\TableFilters;

/**
 * tridy pro filtry tabulek pocitaji s volanim metod Doctrine QueryBuilderu, 
 * ovsem nekdy nutno pouzit nativni SQL. Tato trida umozni volani QueryBuilder metod 
 * a pak to vrati SQL retezece a array s parametry pro doplneni puvodniho SQL dotazu
 */
class QueryBuilderToSqlAdapter
{
    protected string $where_term = '';
    protected array $order_by_terms = [];
    protected array $parameters = [];
    
    public function andWhere(string $sql)
    {
        if (empty($this->where_term)) {
            $this->where_term = $sql;
        } else {
            $this->where_term .= " AND {$sql}";
        }
        
        return $this;
    }
    
    public function orderBy(string $order_by_tableDotColumnName, string $order_direction)
    {
        $this->order_by_terms = [$this->printOrderByString($order_by_tableDotColumnName, $order_direction)];
        return $this;
    }
    
    public function addOrderBy(string $order_by_tableDotColumnName, string $order_direction)
    {
        $this->order_by_terms[] = $this->printOrderByString($order_by_tableDotColumnName, $order_direction);
        return $this;
    }
    
    public function setParameter(string $name, $value)
    {
        $this->parameters[$name] = $value;
        return $this;
    }
    
    protected function printOrderByString(string $order_by_tableDotColumnName, string $order_direction): string
    {
        if (!in_array($order_direction, ['ASC', 'DESC'])) {
            throw new \Exception('pripustne pouze ASC a DESC');
        }
        return "{$order_by_tableDotColumnName} {$order_direction}";
    }
    
    public function getWhereTerm(bool $print_where): string
    {
        $where_word = ($print_where && !empty($this->where_term)) ? 'WHERE ' : '';
        return $where_word . $this->where_term;
    }
    
    public function getOrderByTerm(bool $print_order_by): string
    {
        $order_by_word = ($print_order_by && count($this->order_by_terms) > 0) ? 'ORDER BY ' : '';
        return $order_by_word . implode(', ', $this->order_by_terms);
    }
    
    public function getParameters(): array
    {
        return $this->parameters;
    }
    
}
