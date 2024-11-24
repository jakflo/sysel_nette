<?php
namespace App\UI\Forms;

use \Nette\Application\UI\Form;

class ChangeName
{
    public function create($on_success_object, $on_success_method_name): Form
    {
        $form = new Form();
        $form
                ->setMethod('POST')
                ->addText('name', '')
                ->setRequired()
                ;
        $form->addSubmit('sent', 'Ok');
        $form->addButton('cancel', 'Zrušit');
        $form->addHidden('id');
        $form->onSuccess[] = [$on_success_object, $on_success_method_name];
        
        return $form;
    }
}
