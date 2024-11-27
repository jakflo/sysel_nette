<?php

declare(strict_types=1);

namespace App\UI\ItemsInWarehouse;

use \App\UI\Tools\ArrayTools;
use \Nette\Application\UI\Form;
use \Nette\Application\AbortException;
use \App\UI\Exceptions\NotFoundException;
use \App\UI\ItemsInWarehouse\WarehouseCapacityExceededException;

final class ItemsInWarehousePresenter extends \Nette\Application\UI\Presenter
{
    public function __construct(
            protected \App\UI\ItemsInWarehouse\ItemsInWarehouseModelFactory $items_in_warehouse_model_factory, 
            protected \App\UI\ItemsList\ItemsModelFactory $items_model_factory
    )
    {
        
    }

    public function renderDefault($mode)
    {
        if (!in_array($mode, ['available-only', 'all'])) {
            $this->redirect('ItemsInWarehouse:default');
        }
        
        $model = $this->items_in_warehouse_model_factory->create();
        $model->getEmptyWarehousesListForSelect(true);
        $this->template->title = 'Syslovo sklad | Položky ve skladě';
        $this->template->mode = $mode;
        $this->template->items_list = $model->getList($mode == 'available-only');
        $this->template->empty_warehouses = $model->getEmptyWarehousesListForSelect($mode == 'available-only');
    }
    
    protected function createComponentAddItem(): Form
    {
        $items_model = $this->items_model_factory->create();
        $items_list = ArrayTools::addPlaceholderToArrayForSelect($items_model->printSimpleList());
                
        $form = new Form();
        $form
                ->addSelect('item_id', 'Přidat položku', $items_list)
                ->setRequired()
                ->addRule(Form::IsIn, 'Neplatná položka', array_keys($items_list))
                ;
        $form->addText('lot_name', 'Šarže')->setRequired();
        $form
                ->addInteger('amount')
                ->setRequired()
                ->addRule(Form::MIN, 'Musí být věrší jak 0', 1)
                ;
        $form->addHidden('warehouse_id', 0);
        $form->addSubmit('sent', 'Přidat');
        $form->onSuccess[] = [$this, 'doAddItems'];
        
        return $form;
    }
    
    protected function createComponentAddItemToEmptyWarehouse(): Form
    {
        $items_model = $this->items_model_factory->create();
        $items_in_warehouse_model = $this->items_in_warehouse_model_factory->create();
        $items_list = ArrayTools::addPlaceholderToArrayForSelect($items_model->printSimpleList());
        $empty_warehouses = ArrayTools::addPlaceholderToArrayForSelect($items_in_warehouse_model->getEmptyWarehousesListForSelect($this->getMode() == 'available-only'));
        
        $form = new Form();
        $form
                ->addSelect('warehouse_id', 'Sklad', $empty_warehouses)
                ->setRequired()
                ->addRule(Form::IsIn, 'Neplatný sklad', array_keys($empty_warehouses))
                ;
        $form
                ->addSelect('item_id', 'Přidat položku', $items_list)
                ->setRequired()
                ->addRule(Form::IsIn, 'Neplatná položka', array_keys($items_list))
                ;
        $form->addText('lot_name', 'Šarže')->setRequired();
        $form
                ->addInteger('amount')
                ->setRequired()
                ->addRule(Form::MIN, 'Musí být věrší jak 0', 1)
                ;
        $form->addSubmit('sent', 'Přidat');
        $form->onSuccess[] = [$this, 'doAddItems'];
        
        return $form;
    }
    
    public function handleUpdateMaxItems($warehouse_id, $item_id)
    {
        $items_in_warehouse_model = $this->items_in_warehouse_model_factory->create();
        
        try {
            $response = [
                'maxAmount' => $items_in_warehouse_model->getItemMaxAmount((int)$warehouse_id, (int)$item_id), 
                'status' => 'ok'
            ];
            $this->sendJson($response);
        } catch (AbortException $e) {
            // vnitrni vec Nette, nutno ignorovat
            $this->sendJson($response);
        } catch (\Exception $e) {          
            $response = [
                'status' => 'falied', 
                'error' => $e->getMessage(), 
                'code' => $e->getCode()
            ];
            $this->getHttpResponse()->setCode(400);
            $this->sendJson($response);
        }
    }
    
    public function doAddItems(Form $form, $data)
    {
        $items_in_warehouse_model = $this->items_in_warehouse_model_factory->create();
        
        try {
            $items_in_warehouse_model->addItems((int)$data['warehouse_id'], (int)$data['item_id'], $data['lot_name'], (int)$data['amount']);
            $this->flashMessage('Položky úspěšně vloženy do skladu', 'success');
            $this->redirect('ItemsInWarehouse:default');
        } catch (NotFoundException $e) {
            switch ($e->getCode()) {
                case NotFoundException::WAREHOUSE: 
                    $this->flashMessage('Chyba! Sklad nenalezen', 'error');
                    break;
                case NotFoundException::ITEM:
                    $this->flashMessage('Chyba! položka nenalezena', 'error');
                    break;
                default :
                    throw $e;
            }
            $this->redirect('ItemsInWarehouse:default');
        } catch (WarehouseCapacityExceededException $e) {
            $this->flashMessage('Chyba! Tolik položek se do skladu nevejde', 'error');
            $this->redirect('ItemsInWarehouse:default');
        }
    }
    
    protected function getMode()
    {
        $path = $this->getHttpRequest()->getUrl()->getPath();
        $path_params = explode('/', trim($path, '/'));
        return isset($path_params[1]) ? $path_params[1] : 'all';
    }
    
}
