<?php

declare(strict_types=1);

namespace App\UI\ItemsInWarehouse;

use \App\UI\Tools\ArrayTools;
use \Nette\Application\UI\Form;
use \Nette\Application\AbortException;

final class ItemsInWarehousePresenter extends \Nette\Application\UI\Presenter
{
    public function __construct(
            protected \App\UI\ItemsInWarehouse\ItemsInWarehouseModelFactory $items_in_warehouse_model_factory, 
            protected \App\UI\ItemsList\ItemsModelFactory $items_model_factory
    )
    {
        
    }
    
    protected string $mode;

    public function renderDefault($mode)
    {
        if (!in_array($mode, ['available-only', 'all'])) {
            $this->redirect('ItemsInWarehouse:default');
        }
        $this->mode = $mode;
        
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
        $empty_warehouses = ArrayTools::addPlaceholderToArrayForSelect($items_in_warehouse_model->getEmptyWarehousesListForSelect($this->mode == 'available-only'));
        
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
        
    }
}
