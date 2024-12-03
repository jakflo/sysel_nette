<?php
namespace App\UI\TableFilters;

use \Nette\Application\UI\Form;
use \Doctrine\ORM\QueryBuilder;
use \Doctrine\ORM\Query;

class TableFilterText extends TableFilterBase
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
        $form->addText($this->name . '_value', '');
        return $form;
    }
    
    public function addItemToParamsForLatte(array $params): array
    {
        $params[] = [
            'type' => 'input', 
            'name' => $this->name, 
            'sortable' => $this->sortable
        ];
        return $params;
    }
    
    protected function printContitions(): array
    {
        return [
                'equal' => 'Je shodný', 
                'not_equal' => 'Není shodný', 
                'like' => 'Obsahuje', 
                'not_like' => 'Neobsahuje'
            ];
    }
    
    protected function addWhere(Query|QueryBuilder $query, string $condition, string $value)
    {
        switch ($condition) {
            case 'equal': 
                $query
                    ->andWhere("{$this->tableDotColumnName} = :val")
                    ->setParameter('val', $value)
                    ;
                break;
            case 'not_equal': 
                $query
                    ->andWhere("{$this->tableDotColumnName} != :val")
                    ->setParameter('val', $value)
                    ;
                break;
            case 'like': 
                $query
                    ->andWhere("{$this->tableDotColumnName} LIKE :val")
                    ->setParameter('val', "%$value%")
                    ;
                break;
            case 'not_like': 
                $query
                    ->andWhere("{$this->tableDotColumnName} NOT LIKE :val")
                    ->setParameter('val', "%$value%")
                    ;
                break;
            default :
                throw new \Exception('Nepodporovana podminka filtru');
        }
    }
    
    
    
}
