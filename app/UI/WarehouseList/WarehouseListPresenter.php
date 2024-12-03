<?php
declare(strict_types=1);
namespace App\UI\WarehouseList;

use \Nette\Application\UI\Form;
use \App\UI\Forms\ChangeName;
use \App\UI\Forms\DeleteItem;
use \App\UI\Exceptions\UsedNameException;
use \App\UI\Exceptions\NotFoundException;
use \App\UI\WarehouseList\NotEmptyException;

final class WarehouseListPresenter extends \Nette\Application\UI\Presenter
{
    public function __construct(
        protected \App\UI\WarehouseList\WarehouseModelFactory $model_factory
    )
    {
        
    }
    
    public function renderDefault()
    {
        $model = $this->model_factory->create();
        $warehouse_list = $model->printList();
        $this->template->title = 'Syslovo sklad | Seznam skladů';
        $this->template->warehouse_list = $warehouse_list;
    }
    
    protected function createComponentRenameWarehouse(): Form
    {
        $form = new ChangeName();
        return $form->create($this, 'doRenameWarehouse');
    }
    
    protected function createComponentDeleteWarehouse(): Form
    {
        $form = new DeleteItem();
        return $form->create($this, 'doDeleteWarehouse');
    }
    
    protected function createComponentNewWarehouse(): Form
    {
        $form = new Form();
        $form->setMethod('POST');
        
        $form
                ->addText('name', 'Jméno')
                ->setRequired('Povinný údaj')
                ;
        $form
                ->addInteger('area', 'Plocha v m2')
                ->addRule($form::Min, 'Plocha musí být celé kladné číslo', 1)
                ->setRequired('Povinný údaj')
                ;
        $form->addSubmit('sent', 'Vytořit');
        $form->onSuccess[] = [$this, 'doNewWarehouse'];
        
        return $form;
    }
    
    public function doRenameWarehouse(Form $form, $data)
    {
        $model = $this->model_factory->create();
        
        try {
            $model->rename((int)$data['id'], $data['name']);
            $this->flashMessage('Sklad přejmenován', 'success');
            $this->redirect('warehouseList:default');
        } catch (UsedNameException $e) {
            $this->flashMessage('Chyba! Jméno skladu je již použito', 'error');
            $this->redirect('warehouseList:default');
        } catch (NotFoundException $e) {
            $this->flashMessage('Chyba! Sklad nenalezen', 'error');
            $this->redirect('warehouseList:default');
        }
    }
    
    public function doDeleteWarehouse(Form $form, $data)
    {
        $model = $this->model_factory->create();
        
        try {
            $model->delete((int)$data['id']);
            $this->flashMessage('Sklad smazán', 'success');
            $this->redirect('warehouseList:default');
        } catch (NotFoundException $e) {
            $this->flashMessage('Chyba! Sklad nenalezen', 'error');
            $this->redirect('warehouseList:default');
        } catch (NotEmptyException $e) {
            $this->flashMessage('Chyba! Sklad není prázdný', 'error');
            $this->redirect('warehouseList:default');
        }
    }
    
    public function doNewWarehouse(Form $form, $data)
    {
        $model = $this->model_factory->create();
        
        try {
            $model->create($data['name'], (int)$data['area']);
            $this->flashMessage('Sklad vytvořen', 'success');
            $this->redirect('warehouseList:default');
        } catch (UsedNameException $e) {
            $form->addError('Jméno skladu je již použito');
        }
    }
    
}
