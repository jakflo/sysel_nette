<?php
declare(strict_types=1);
namespace App\UI\ItemsLotList;

use \Nette\Application\UI\Form;
use \App\UI\Forms\ChangeName;
use \App\UI\Forms\DeleteItem;
use \App\UI\Model\SqlPaginator;
use \App\UI\Exceptions\NotFoundException;
use \App\UI\Exceptions\UsedNameException;
use \App\UI\ItemsLotList\ItemLotIsUsedException;

class ItemsLotListPresenter extends \Nette\Application\UI\Presenter
{
    protected SqlPaginator $item_lot_list;
    public function __construct(
            protected \App\UI\ItemsLotList\ItemsLotModelFactory $items_lot_model_factory, 
            protected \App\UI\ItemsList\ItemsModelFactory $items_model_factory
    )
    {
        
    }
    
    public function renderDefault(int $id)
    {
        $filters = $this->getFilters()->addItemToParamsForLatte();
        $pages = $this->getItemLotList();
        
        $this->template->title = 'Syslovo sklad | Seznam šarží položky';
        $this->template->itemId = $id;
        $this->template->itemName = $this->items_model_factory->create()->getItem($id)->getName();
        $this->template->lotList = $pages->getRows();
        $this->template->lotListFilters = $filters;
        $this->template->paginator = $pages;
    }
    
    protected function createComponentItemsLotListFilters(): Form {
        $form = new Form();
        $form->setMethod('GET');
        $this->getFilters()->addItemToFormComponents($form);
        $form->addSubmit('filter', 'Filtrovat');
        $form->onSuccess[] = [$this, 'itemsLotListFilters'];
        return $form;
    }
    
    protected function createComponentRenameItemLot(): Form
    {
        $form = (new ChangeName())->create($this, 'doRenameItemLot');
        $form->addHidden('filters', $this->getEncodedParams());
        return $form;
    }
    
    protected function createComponentDeleteItemLot(): Form
    {
        $form = (new DeleteItem())->create($this, 'doDeleteItemLot');
        $form->addHidden('filters', $this->getEncodedParams());
        return $form;
    }
    
    protected function createComponentNewItemLot(): Form
    {
        $form = new Form();
        $form->addText('name', 'Šarže')->setRequired();
        $form->addHidden('filters', $this->getEncodedParams());
        $form->addSubmit('sent', 'Vytvořit');
        $form->onSuccess[] = [$this, 'doCreateItemLot'];
        return $form;
    }
    
    public function itemsLotListFilters(Form $form, $data)
    {
        
    }
    
    public function doRenameItemLot(Form $form, $data)
    {
        $model = $this->items_lot_model_factory->create();
        try {
            $model->renameItemLot((int)$data['id'], $data['name']);
            $this->flashMessage('Šarže položky byla přejmenována', 'success');
            $this->myRedirect($data['filters']);
        } catch (UsedNameException $e) {
            $this->flashMessage('Chyba! Jméno šarže je již použito', 'error');
            $this->myRedirect($data['filters']);
        } catch (NotFoundException $e) {
            $this->flashMessage('Chyba! Šarže nebyla nalezena', 'error');
            $this->myRedirect($data['filters']);
        }
    }
    
    public function doDeleteItemLot(Form $form, $data)
    {
        $model = $this->items_lot_model_factory->create();
        try {
            $model->deleteItemLot((int)$data['id']);
            $this->flashMessage('Šarže položky byla odstraněna', 'success');
            $this->myRedirect($data['filters']);
        } catch (NotFoundException $e) {
            $this->flashMessage('Chyba! Šarže nebyla nalezena', 'error');
            $this->myRedirect($data['filters']);
        } catch (ItemLotIsUsedException $e) {
            $this->flashMessage('Chyba! Šarže je již použita', 'error');
            $this->myRedirect($data['filters']);
        }
    }
    
    public function doCreateItemLot(Form $form, $data)
    {
        $model = $this->items_lot_model_factory->create();
        try {
            $model->createItemWithLot($this->getId(), $data['name']);
            $this->flashMessage('Šarže byla úspěšně vytvořena', 'success');
            $this->myRedirect($data['filters']);
        } catch (UsedNameException $e) {
            $this->flashMessage('Chyba! Jméno šarže je již použito', 'error');
            $this->myRedirect($data['filters']);
        }
    }
    
    protected function getFilters(): \App\UI\ItemsLotList\ItemsLotListFilers
    {
        return new \App\UI\ItemsLotList\ItemsLotListFilers();
    }
    
    public function getItemLotList(): SqlPaginator
    {
        if (!isset($this->item_lot_list)) {
            $this->item_lot_list = $this->items_lot_model_factory->create()->getItemWithLotList($this->getHttpRequest(), $this->getId());
        }
        return $this->item_lot_list;
    }
    
    protected function getId(): int
    {
        $path = $this->getHttpRequest()->getUrl()->getPath();
        $path_params = explode('/', trim($path, '/'));
        return (int)$path_params[1];
    }
    
    protected function getUrlAfterFormSubmit()
    {
        return (string)$this->getHttpRequest()->getUrl()->withQueryParameter('filter', null)->withQueryParameter('do', null);
    }
    
    // zakoduje GET parametry do json a to do base64
    protected function getEncodedParams(): string
    {
        return base64_encode(json_encode($this->getHttpRequest()->getQuery()));
    }
    
    /**
     * zajisti zachovani get parametru pro filtry a zobrazeni flash zpravy
     * @param type $params GET parametry filtru zakodovanych self::encodeParams()
     */
    protected function myRedirect(string $params)
    {
        $stored_params = json_decode(base64_decode($params), true);
        $request = $this->getRequest();        
        $new_params = array_merge($stored_params, $request->getParameters());
        
        unset($new_params[self::FlashKey]);
        $flash_key = $this->getParameter(self::FlashKey);
        if ($flash_key) {
            $new_params[self::FlashKey] = $flash_key;
        }
        
        $request->setParameters($new_params);
        $this->redirectUrl($this->getLinkGenerator()->requestToUrl($request));
    }
    
}
