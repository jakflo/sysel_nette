<?php
namespace App\UI\TableFilters;

use \Nette\Application\UI\Form;
use \Doctrine\ORM\QueryBuilder;
use \Doctrine\ORM\Query;
use \App\UI\TableFilters\QueryBuilderToSqlAdapter;

class TableFilterDate extends TableFilterBase
{
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
        $form->addDate($this->name . '_value');
        $form->addDate($this->name . '_value_2');
        return $form;
    }
    
    public function addItemToParamsForLatte(array $params): array
    {
        $params[] = [
            'type' => 'date', 
            'name' => $this->name, 
            'sortable' => $this->sortable
        ];
        return $params;
    }
    
    protected function printContitions(): array
    {
        return [
                'equal' => 'Je shodný', 
                'less_than' => 'Je menší', 
                'greater_than' => 'Je větší', 
                'between' => 'Mezi'
            ];
    }
    
    protected function addWhere(Query|QueryBuilder|QueryBuilderToSqlAdapter $query, string $condition, string $value, string|null $value_2)
    {
        if (empty($value)) {
            return;
        }
        switch ($condition) {
            case 'equal': 
                $start_date = $value . ' 00:00:00';
                $end_date = $value . ' 23:59:59';
                $this->addWhereBetweenDates($query, $start_date, $end_date);
                break;
            case 'less_than':
                $end_date = $value . ' 00:00:00';
                $query->andWhere("{$this->tableDotColumnName} <= :val_{$this->printHashedName($this->name. '_1')}")
                    ->setParameter('val_' . $this->printHashedName($this->name. '_1'), $end_date)
                    ;
                break;
            case 'greater_than':
                $start_date = $value . ' 23:59:59';
                $query->andWhere("{$this->tableDotColumnName} >= :val_{$this->printHashedName($this->name. '_1')}")
                    ->setParameter('val_' . $this->printHashedName($this->name. '_1'), $start_date)
                    ;
                break;
            case 'between': 
                if (!empty($value_2)) {
                    $start_date = $value . ' 00:00:00';
                    $end_date = $value_2 . ' 23:59:59';
                    $this->addWhereBetweenDates($query, $start_date, $end_date);
                }
                break;
            default :
                throw new \Exception('Neznama podminka');
        }
    }
    
    protected function addWhereBetweenDates(Query|QueryBuilder|QueryBuilderToSqlAdapter $query, string $start_date, string $end_date)
    {
        $query->andWhere("{$this->tableDotColumnName} >= :val_{$this->printHashedName($this->name. '_1')}")
                ->setParameter('val_' . $this->printHashedName($this->name. '_1'), $start_date)
                ->andWhere("{$this->tableDotColumnName} <= :val_{$this->printHashedName($this->name. '_2')}")
                ->setParameter('val_' . $this->printHashedName($this->name. '_2'), $end_date)
                ;
    }
    
    public function addItemFormOnSubmit(Form $form, $data)
    {
        if ($data[$this->name . '_cond'] == 'between' && empty($data[$this->name . '_value_2'])) {
            $form[$this->name . '_cond']->addError('U filtru typu mezi nemůže být datum do prázdné');
        }
        else if ($data[$this->name . '_cond'] == 'between' && $data[$this->name . '_value'] > $data[$this->name . '_value_2']) {
            $form[$this->name . '_cond']->addError('U filtru typu mezi nemůže být datum do menší než datum od');
        }
    }
    
}
