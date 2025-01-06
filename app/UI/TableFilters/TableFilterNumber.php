<?php
namespace App\UI\TableFilters;

use \Nette\Application\UI\Form;
use \Doctrine\ORM\QueryBuilder;
use \Doctrine\ORM\Query;
use \App\UI\TableFilters\QueryBuilderToSqlAdapter;

class TableFilterNumber extends TableFilterBase
{
    use \App\UI\TableFilters\BetweenValuesTrait;
    
    protected bool $is_integer = false;
    protected float|null $min = null;
    protected float|null $max = null;
    protected bool $can_be_null = false;

    public function __construct(
            protected string $name, 
            protected string $label, 
            protected string $tableDotColumnName, 
            protected bool $sortable
    )
    {
        
    }
    
    public function addItemToFormComponent(Form $form): Form {
        $form
                ->addSelect($this->name . '_cond', $this->label, $this->printContitions())
                ->addRule(Form::IsIn, 'neplatná podmínka', array_keys($this->printContitions()))
                ;
        if ($this->is_integer) {
            $value_1 = $form->addInteger($this->name . '_value', '');
            $value_2 = $form->addInteger($this->name . '_value_2', '');
        } else {
            $value_1 = $form->addFloat($this->name . '_value', '');
            $value_2 = $form->addFloat($this->name . '_value_2', '');
        }
        
        if ($this->min !== null) {
            $value_1->addRule(Form::MIN, 'Hodnota příliš nízká', $this->min);
        }
        if ($this->max !== null) {
            $value_1->addRule(Form::MAX, 'Překročena maximální hodnota', $this->max);
            $value_2->addRule(Form::MAX, 'Překročena maximální hodnota', $this->max);
        }
        
        return $form;
    }
    
    public function addItemToParamsForLatte(array $params): array
    {
        $params[] = [
            'type' => 'number', 
            'name' => $this->name, 
            'sortable' => $this->sortable
        ];
        return $params;
    }
    
    protected function printContitions(): array
    {
        $contitions =  [
            'equal' => 'Je shodný', 
            'not_equal' => 'Není shodný', 
            'less_than' => 'Je menší', 
            'greater_than' => 'Je větší', 
            'between' => 'Mezi'
        ];
        
        if ($this->can_be_null) {
            $contitions['is_empty'] = 'Je prázdné';
            $contitions['not_empty'] = 'Není prázdné';
        }
        
        return $contitions;
    }
    
    protected function addWhere(Query|QueryBuilder|QueryBuilderToSqlAdapter $query, string $condition, string $value, string|null $value_2)
    {
        switch ($condition) {
            case 'equal': 
                $query
                    ->andWhere("{$this->tableDotColumnName} = :val_{$this->printHashedName($this->name. '_1')}")
                    ->setParameter('val_' . $this->printHashedName($this->name. '_1'), $value)
                    ;
                break;
            case 'not_equal': 
                $query
                    ->andWhere("{$this->tableDotColumnName} != :val_{$this->printHashedName($this->name. '_1')}")
                    ->setParameter('val_' . $this->printHashedName($this->name. '_1'), $value)
                    ;
                break;
            case 'less_than':
                $query->andWhere("{$this->tableDotColumnName} <= :val_{$this->printHashedName($this->name. '_1')}")
                    ->setParameter('val_' . $this->printHashedName($this->name. '_1'), $value)
                    ;
                break;
            case 'greater_than':
                $query->andWhere("{$this->tableDotColumnName} >= :val_{$this->printHashedName($this->name. '_1')}")
                    ->setParameter('val_' . $this->printHashedName($this->name. '_1'), $value)
                    ;
                break;
            case 'between':
                $this->addWhereBetweenValues($query, $value, $value_2);
                break;
            case 'is_empty':
                $query->andWhere("{$this->tableDotColumnName} IS NULL");
                break;
            case 'not_empty':
                $query->andWhere("{$this->tableDotColumnName} IS NOT NULL");
                break;
            default :
                throw new \Exception('Nepodporovana podminka filtru');
        }
    }
    
    public function setIsInteger(bool $is_integer)
    {
        $this->is_integer = $is_integer;
        return $this;
    }
    
    public function setMin(float $min)
    {
        $this->min = $min;
        return $this;
    }
    
    public function setMax(float $max)
    {
        $this->max = $max;
        return $this;
    }
    
    public function setCanBeNull(bool $can_be_null)
    {
        $this->can_be_null = $can_be_null;
        return $this;
    }
    
}
