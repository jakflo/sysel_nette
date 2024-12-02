<?php
declare(strict_types=1);

namespace App\UI\ItemsLotList;

class ItemsLotListPresenter extends \Nette\Application\UI\Presenter
{
    
    public function renderDefault(int $id)
    {
        $this->template->title = 'Syslovo sklad | Seznam šarží položky';
//        $this->template->items_list = $model->printList();
    }
    
}
