<?php

declare(strict_types=1);

namespace App\UI\ItemsList;

use \App\UI\Tools\ArrayTools;
use \Nette\Application\UI\Form;
use \App\UI\Forms\ChangeName;
use App\UI\Forms\DeleteItem;
use \App\UI\ItemsList\ItemIsUsedException;
use \App\UI\Exceptions\NotFoundException;
use \App\UI\Exceptions\UsedNameException;

final class ItemsListPresenter extends \Nette\Application\UI\Presenter
{
    public function __construct(
            protected \App\UI\ItemsList\ItemsModelFactory $items_model_factory, 
            protected \App\UI\ManufacturerList\ManufacturerModelFactory $manufacturer_model_factory
    )
    {
        
    }
    
    public function renderDefault()
    {
        $model = $this->items_model_factory->create();
        $this->template->title = 'Syslovo sklad | Seznam položek';
        $this->template->items_list = $model->printList();
    }
    
    protected function createComponentChangeArea(): Form
    {
        $form = new Form();
        $form->addFloat('area', '')
                ->setRequired()
                ->addRule(Form::Min, 'Hodnota musí být větší než 0', 0.0000000000000000000001)
                ;
        $form->addHidden('id', 0);
        $form->addHidden('old_value', 0);
        $form->addSubmit('sent', 'změnit');
        $form->addButton('back', 'zpět');
        $form->onSuccess[] = [$this, 'doChangeArea'];
        
        return $form;
    }
    
    protected function createComponentRenameItem(): Form
    {
        $form = new ChangeName();
        return $form->create($this, 'doRenameItem');
    }
    
    protected function createComponentDeleteItem(): Form
    {
        $form = new DeleteItem();
        return $form->create($this, 'doDeleteItem');
    }
    
    protected function createComponentCreateItem(): Form
    {
        $manufacturer_model = $this->manufacturer_model_factory->create();
        $manufacturers = ArrayTools::addPlaceholderToArrayForSelect($manufacturer_model->getListForSelect());
        $form = new Form();
        $form->addText('name', 'Jméno')->setRequired();
        $form->addFloat('area', 'Zabraná plocha (m2 / ks)')
                ->setRequired()
                ->addRule(Form::Min, 'Hodnota musí být větší než 0', 0.0000000000000000000001)
                ;
        $form->addSelect('manufacturer_id', 'Výrobce', $manufacturers)->setRequired();
        $form->addSubmit('sent', 'Vytvořit');
        $form->onSuccess[] = [$this, 'doCreateItem'];
        
        return $form;
    }
    
    public function doChangeArea(Form $form, $data)
    {
        $model = $this->items_model_factory->create();
        
        try {
            $model->changeArea((int)$data['id'], (float)$data['area']);
            $this->flashMessage('Plocha položky byla změněna', 'success');
            $this->redirect('itemsList:default');
        } catch (ItemIsUsedException $e) {
            $this->flashMessage('Chyba! Položka je již použita', 'error');
            $this->redirect('itemsList:default');
        } catch (NotFoundException $e) {
            $this->flashMessage('Chyba! Položka nebyla nalezena', 'error');
            $this->redirect('itemsList:default');
        }
    }
    
    public function doRenameItem(Form $form, $data)
    {
        $model = $this->items_model_factory->create();
        
        try {
            $model->changeName((int)$data['id'], $data['name']);
            $this->flashMessage('Položka byla přejmenována', 'success');
            $this->redirect('itemsList:default');
        } catch (UsedNameException $e) {
            $this->flashMessage('Chyba! Jméno položky je již použito', 'error');
            $this->redirect('itemsList:default');
        } catch (NotFoundException $e) {
            $this->flashMessage('Chyba! Položka nebyla nalezena', 'error');
            $this->redirect('itemsList:default');
        }
    }
    
    public function doDeleteItem(Form $form, $data)
    {
        $model = $this->items_model_factory->create();
        
        try {
            $model->delete((int)$data['id']);
            $this->flashMessage('Položka byla smazána', 'success');
            $this->redirect('itemsList:default');
        } catch (NotFoundException $e) {
            $this->flashMessage('Chyba! Položka nenalezena', 'error');
            $this->redirect('itemsList:default');
        } catch (ItemIsUsedException $e) {
            $this->flashMessage('Chyba! Položka je použita', 'error');
            $this->redirect('itemsList:default');
        }
    }
    
    public function doCreateItem(Form $form, $data)
    {
        $model = $this->items_model_factory->create();
        try {
            $model->create($data['name'], (float)$data['area'], (int)$data['manufacturer_id']);
            $this->flashMessage('Položka byla vytvořena', 'success');
            $this->redirect('itemsList:default');
        } catch (UsedNameException $e) {
            $form->addError('Jméno položky je již použito');
        } catch (NotFoundException $e) {
            $form->addError('Výrobce nenalezen');
        }
    }
    
}
