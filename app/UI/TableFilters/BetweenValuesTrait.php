<?php
namespace App\UI\TableFilters;

use \Nette\Application\UI\Form;
use \Doctrine\ORM\QueryBuilder;
use \Doctrine\ORM\Query;
use \App\UI\TableFilters\QueryBuilderToSqlAdapter;

trait BetweenValuesTrait
{
    protected function addWhereBetweenValues(Query|QueryBuilder|QueryBuilderToSqlAdapter $query, string $value_1, string $value_2)
    {
        $query->andWhere("{$this->tableDotColumnName} >= :val_{$this->printHashedName($this->name. '_1')}")
                ->setParameter('val_' . $this->printHashedName($this->name. '_1'), $value_1)
                ->andWhere("{$this->tableDotColumnName} <= :val_{$this->printHashedName($this->name. '_2')}")
                ->setParameter('val_' . $this->printHashedName($this->name. '_2'), $value_2)
                ;
    }
    
    public function addItemFormOnSubmit(Form $form, $data)
    {
        if ($data[$this->name . '_cond'] == 'between' && empty($data[$this->name . '_value_2'])) {
            $form[$this->name . '_cond']->addError('U filtru typu Mezi nemůže být 2. hodnota prázdná');
        }
        else if ($data[$this->name . '_cond'] == 'between' && $data[$this->name . '_value'] > $data[$this->name . '_value_2']) {
            $form[$this->name . '_cond']->addError('U filtru typu Mezi nemůže být 2. hodnota menší než 1. hodnota');
        }
    }
    
}
