<?php
namespace App\UI\TableFilters;

use \Nette\Application\UI\Form;
use \Doctrine\ORM\QueryBuilder;
use \Doctrine\ORM\Query;
use \App\UI\TableFilters\QueryBuilderToSqlAdapter;

class TableFilterSortOnly extends TableFilterBase
{
    protected bool $sortable = true;

    public function __construct(
            protected string $name, 
            protected string $label, 
            protected string $tableDotColumnName
    )
    {
        
    }
    
    public function addItemToFormComponent(Form $form): Form {
        return $form;
    }
    
    public function addItemToParamsForLatte(array $params): array
    {
        $params[] = [
            'type' => 'none', 
            'name' => $this->name, 
            'label' => $this->label, 
            'sortable' => true
        ];
        return $params;
    }
    
    protected function printContitions(): array
    {
        return [];
    }
    
    protected function addWhere(Query|QueryBuilder|QueryBuilderToSqlAdapter $query, string $condition, string $value, string|null $value_2)
    {
        return $query;
    }
    
}
