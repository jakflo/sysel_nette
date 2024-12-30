<?php
namespace App\UI\TableFilters;

use \Nette\Application\UI\Form;
use \Doctrine\ORM\QueryBuilder;
use \Doctrine\ORM\Query;
use \App\UI\TableFilters\QueryBuilderToSqlAdapter;
use \App\UI\Tools\ArrayTools;

class TableFilterSelect extends TableFilterBase
{
    public function __construct(
            protected string $name, 
            protected string $label, 
            protected string $tableDotColumnName, 
            protected array $items, 
            protected bool $sortable
    )
    {
        $this->items = ArrayTools::addPlaceholderToArrayForSelect($this->items);
    }
    
    public function addItemToFormComponent(Form $form): Form {
        $form
                ->addSelect($this->name . '_cond', $this->label, $this->printContitions())
                ->addRule(Form::IsIn, 'neplatná podmínka', array_keys($this->printContitions()))
                ;
        $form
                ->addSelect($this->name . '_value', '', $this->items)
                ->addRule(Form::IsIn, 'neplatná hodnota', array_keys($this->items))
                ;
        return $form;
    }
    
    public function addItemToParamsForLatte(array $params): array
    {
        $params[] = [
            'type' => 'select', 
            'name' => $this->name, 
            'sortable' => $this->sortable
        ];
        return $params;
    }
    
    protected function printContitions(): array
    {
        return [
                'equal' => 'Je shodný', 
                'not_equal' => 'Není shodný'
            ];
    }
    
    protected function addWhere(Query|QueryBuilder|QueryBuilderToSqlAdapter $query, string $condition, string $value, string|null $value_2)
    {
        switch ($condition) {
            case 'equal': 
                $query
                    ->andWhere("{$this->tableDotColumnName} = :val_{$this->printHashedName($this->name)}")
                    ->setParameter('val_' . $this->printHashedName($this->name), $value)
                    ;
                break;
            case 'not_equal': 
                $query
                    ->andWhere("{$this->tableDotColumnName} != :val_{$this->printHashedName($this->name)}")
                    ->setParameter('val_' . $this->printHashedName($this->name), $value)
                    ;
                break;
            default :
                throw new \Exception('Nepodporovana podminka filtru');
        }
    }
}
