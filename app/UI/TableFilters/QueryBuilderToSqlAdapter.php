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
            $this->where_term = " AND {$sql}";
        }
        
        return $this;
    }
}
