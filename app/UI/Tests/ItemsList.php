<?php
namespace App\UI\Tests;

use \Tester\Assert;
use \App\UI\Entities\Item;
use \App\UI\Exceptions\UsedNameException;
use \App\UI\ItemsList\ItemIsUsedException;

class ItemsList extends \App\UI\Tests\TestCase
{
    public function __construct(
            protected \App\UI\Tests\DataCreatorFactory $data_creator_factory, 
            protected \Doctrine\ORM\EntityManager $em, 
            protected \App\UI\ItemsList\ItemsModelFactory $items_model_factory
    )
    {
        
    }

    public function testCreate()
    {
        $new_name = 'item_oqixcjhfdskfhkshxyqww';
        $this->data_creator_factory->create()->createItem($new_name);
        $new_item = $this->em->getRepository(Item::class)->findOneByName($new_name);
        Assert::type(Item::class, $new_item);
        
        $toto = $this;
        Assert::exception(
                function() use($toto, $new_name) {
                    $toto->data_creator_factory->create()->createItem($new_name);
                }, 
                UsedNameException::class
        );
    }
    
    public function testRename()
    {
        $name = 'item_oqixcjhfdskfhkshxyqww';
        $new_id = $this->data_creator_factory->create()->createItem($name);
        $new_name = 'item_oqixcjhfdskfhkshxyqww2';
        $this->items_model_factory->create()->changeName($new_id, $new_name);
        $renamed_item = $this->em->getRepository(Item::class)->findOneByName($new_name);
        Assert::type(Item::class, $renamed_item);
        
        $toto = $this;
        Assert::exception(
                function() use($toto, $new_id, $new_name) {
                    $toto->items_model_factory->create()->changeName($new_id, $new_name);
                }, 
                UsedNameException::class
        );
    }
    
    public function testDelete()
    {
        $data_creator = $this->data_creator_factory->create();
        $item_model = $this->items_model_factory->create();
        $name = 'item_oqixcjhfdskfhkshxyqww';
        $new_item_id = $data_creator->createItem($name);
        $item_model->delete($new_item_id);
        $item = $this->em->getRepository(Item::class)->findOneByName($name);
        Assert::null($item);
        
        $item_in_new_warehouse = $data_creator->createItemInNewWarehouse('warehouse_mvcklmladsadccvnd', $name, 'lot_dsajhdjkhashhvc');
        $new_item_id_2 = $item_in_new_warehouse['item_id'];
        
        Assert::exception(
                function() use($item_model, $new_item_id_2) {
                    $item_model->delete($new_item_id_2);
                }, 
                ItemIsUsedException::class
        );
    }
    
    public function testChangeArea()
    {
        $data_creator = $this->data_creator_factory->create();
        $item_model = $this->items_model_factory->create();
        $name = 'item_oqixcjhfdskfhkshxyqww';
        $name_2 = 'item_oqixcjhfdskfhkshxyqww2';
        $new_item_id = $data_creator->createItem($name);
        $item_model->changeArea($new_item_id, 4);
        $item = $item_model->getItem($new_item_id);
        Assert::equal((int)$item->getArea(), 4);
        
        $item_in_new_warehouse = $data_creator->createItemInNewWarehouse('warehouse_mvcklmladsadccvnd', $name_2, 'lot_dsajhdjkhashhvc');
        $new_item_id_2 = $item_in_new_warehouse['item_id'];
        
        Assert::exception(
                function() use($item_model, $new_item_id_2) {
                    $item_model->changeArea($new_item_id_2, 4);
                }, 
                ItemIsUsedException::class
        );
    }
    
}
