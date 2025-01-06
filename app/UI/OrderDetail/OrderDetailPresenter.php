<?php
namespace App\UI\OrderDetail;

use \Nette\Application\UI\Form;
use \App\UI\Exceptions\NotFoundException;
use \App\UI\OrderDetail\OrderDetailException;

class OrderDetailPresenter extends \Nette\Application\UI\Presenter
{
    public function __construct(
            protected \App\UI\OrderDetail\OrderDetailModelFactory $order_detail_model_factory, 
            protected \App\UI\FindItems\FindItemsModelFactory $find_items_model_factory
    )
    {
        
    }
    
    public function renderDefault(int $id)
    {
        $this->template->order_detail = $this->getOrderDetails();
        
        $this->template->client_info = $this->getOrderDetailModel()->getClientInfo();
        $this->template->items = $this->getOrderDetailModel()->getItemsInOrder();
        $this->template->title = 'Syslovo sklad | Detail objednávky';
    }
    
    public function renderAssignItems(int $id)
    {
        $this->template->order_detail = $this->getOrderDetails();
        $this->template->items_requested = $this->getOrderDetailModel()->getItemsInOrder();
        $this->template->items_query = $this->getOrderDetailModel()->getItemsQuery();
        
        $this->template->title = 'Syslovo sklad | Přiřazení položek';
    }
    
    protected function createComponentChangeOrderStatus(): Form
    {
        $order_detail = $this->getOrderDetails();
        $allowed_statuses = $this->getOrderDetailModel()->getAllowedStatusesForSelect();
        
        $form = new Form();
        $form->addSelect('new_status', null, $allowed_statuses)
                ->addRule(Form::IsIn, 'Nepovolená změna stavu', array_keys($allowed_statuses))
                ->setValue($order_detail['status_shortname'])
                ;
        $form->addHidden('order_id', $this->getId());
        $form->addHidden('current_state', $order_detail['status_shortname']);
        $form->addSubmit('sent', 'Změnit');
        $form->onSuccess[] = [$this, 'doChangeStatus'];
        
        return $form;
    }
    
    protected function createComponentChoosePreferedWarehouses(): Form
    {
        $items_query = $this->getOrderDetailModel()->getItemsQuery();
        $form = new Form();
        
        $warehouses_with_all_items = array_keys($items_query->getWarehousesWithAllItems());
        $warehouses_with_some_item = $items_query->getWarehousesWithSomeItem();
        
        foreach ($warehouses_with_some_item as $warehouse_id => &$warehouse_name) {
            if (in_array($warehouse_id, $warehouses_with_all_items)) {
                $warehouse_name .= ' (sklad obsahuje všechny objednané položky)';
            }
        }
        
        $form->addCheckboxList('prefered_warehouses', '', $warehouses_with_some_item)
                ->setHtmlAttribute('class', 'prefered_warehouses_cb')
                ;
        $form->addSubmit('sent', 'Přiřadit položky');
        $form->addButton('cancel_selection', 'Zrušit výběr');
        $form->onSuccess[] = [$this, 'doAssignItems'];
        
        return $form;
    }
    
    public function doChangeStatus(Form $form, array $data)
    {
        $order_detail = $this->getOrderDetails();
        $current_state = $order_detail['status_shortname'];
        $allowed_states = $this->getOrderDetailModel()->getAllowedStatusChanges($current_state);
        
        if ($current_state != $data['current_state']) {
            $this->flashMessage('Před odesláním formuláře došlo ke změně stavu objednávky.', 'error');
            $this->redirect('OrderDetail:default', ['id' => $this->getId()]);
        }
        if (!in_array($data['new_status'], $allowed_states)) {
            $this->flashMessage('Chyba! Nepovolená změna stavu objednávky.', 'error');
            $this->redirect('OrderDetail:default', ['id' => $this->getId()]);
        }
        if ($current_state == 'new' && $data['new_status'] == 'items_reserved') {
            $this->redirect('OrderDetail:assignItems', ['id' => $this->getId()]);
        }
    }
    
    public function doAssignItems(Form $form, array $data)
    {
        try {
            $this->getOrderDetailModel()->assignItemsToOrder($data['prefered_warehouses']);
            $this->flashMessage('Položky byly přiřazeny, stav objednávky byl změněn na Rezervováno.', 'success');
            $this->redirect('Orders:default');
        } catch (OrderDetailException $e) {
            if ($e->getCode() === OrderDetailException::ORDERISNOTNEW) {
                $this->flashMessage('Chyba! Stav objednávky již byl změněn, není ve stavu Nová.', 'error');
                $this->redirect('Orders:default');
            } elseif ($e->getCode() === OrderDetailException::NOTALLITEMSFOUND) {
                $this->flashMessage('Chyba! Ve skladech není dostatek položek.', 'error');
                $this->redirect('OrderDetail:assignItems', ['id' => $this->getId()]);
            } else {
                throw $e;
            }
        }
    }
    
    protected function getOrderDetails(): array
    {
        try {
            $order_detail = $this->getOrderDetailModel()->getOrderDetails();
        } catch (NotFoundException $e) {
            if ($e->getCode() !== NotFoundException::ORDER) {
                throw $e;
            }
            $this->flashMessage('Objednávka nenalezena', 'error');
            $this->redirect('Orders:default');
        }
        
        return $order_detail;
    }
    
    protected function getOrderDetailModel(): \App\UI\OrderDetail\OrderDetailModel
    {
        static $model = null;
        
        if ($model === null) {
            $model = $this->order_detail_model_factory->create($this->getId());
        }
        
        return $model;
    }
    
    protected function getId(): int
    {
        $path = $this->getHttpRequest()->getUrl()->getPath();
        $path_params = explode('/', trim($path, '/'));
        return (int)$path_params[1];
    }
    
}
