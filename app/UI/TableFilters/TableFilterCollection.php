<?php
namespace App\UI\TableFilters;

use \Nette\Application\UI\Form;
use \Doctrine\ORM\QueryBuilder;
use \Doctrine\ORM\Query;
use \App\UI\TableFilters\QueryBuilderToSqlAdapter;
use \Nette\Http\IRequest;

abstract class TableFilterCollection
{
    protected array $table_filters;
    
    public function __construct()
    {
        $this->setFilters();
    }
    
    abstract public function setFilters();
    
    public function addFilter(TableFilterBase $filter)
    {
        $this->table_filters[] = $filter;
        return $this;
    }
    
    public function addItemToFormComponents(Form $form): Form
    {
        foreach ($this->table_filters as $filter) {
            $filter->addItemToFormComponent($form);
        }
        return $form;
    }
    
    public function addItemToParamsForLatte(array $params = []): array
    {
        foreach ($this->table_filters as $filter) {
            $params = $filter->addItemToParamsForLatte($params);
        }
        return $params;
    }
    
    public function applyFilters(Query|QueryBuilder|QueryBuilderToSqlAdapter $query, IRequest $request): Query|QueryBuilder|QueryBuilderToSqlAdapter
    {
        foreach ($this->table_filters as $filter) {
            $filter->applyFilter($query, $request);
        }
        return $query;
    }
    
}
