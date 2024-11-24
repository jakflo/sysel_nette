<?php
namespace App\UI\Forms;

use \Nette\Application\UI\Form;

class DeleteItem
{
    public function create($on_success_object, $on_success_method_name): Form
    {
        $form = new Form();
        $form->setMethod('POST');
        $form->addSubmit('sent', 'Smazat');
        $form->addHidden('id');
        $form->onSuccess[] = [$on_success_object, $on_success_method_name];
        return $form;
    }
}
