<?php
namespace tests\Classes;

use \Tester\Assert;
use \App\UI\Entities\Warehouse;
use \App\UI\Tools\ArrayTools;
use \App\UI\Exceptions\UsedNameException;
use \App\UI\WarehouseList\NotEmptyException;

class WarehouseListTestClass extends SyselTestCase
{
    public function __construct(
            protected \App\UI\WarehouseList\WarehouseModelFactory $warehouse_model_factory, 
            protected \tests\Classes\DataCreatorFactory $data_creator_factory, 
            protected \Doctrine\ORM\EntityManager $em
    )
    {
        
    }  
    
    public function testCreate()
    {
        $new_name = 'test_oqixcjhfdskfhkshxyqww';
        $this->data_creator_factory->create()->createWarehouse($new_name);
        $new_warehouse = $this->em->getRepository(Warehouse::class)->findOneByName($new_name);
        Assert::type(Warehouse::class, $new_warehouse);
        
        $toto = $this;
        Assert::exception(
                function() use($toto, $new_name) {
                    $toto->data_creator_factory->create()->createWarehouse($new_name);
                }, 
                UsedNameException::class
        );
    }
    
    public function testRename()
    {
        $name = 'test_oqixcjhfdskfhkshxyqww';
        $new_id = $this->data_creator_factory->create()->createWarehouse($name);
        $new_name = 'test_oqixcjhfdskfhkshxyqww2';
        $this->warehouse_model_factory->create()->rename($new_id, $new_name);
        $renamed_warehouse = $this->em->getRepository(Warehouse::class)->findOneByName($new_name);
        Assert::type(Warehouse::class, $renamed_warehouse);
        
        $toto = $this;
        Assert::exception(
                function() use($toto, $new_id, $new_name) {
                    $toto->warehouse_model_factory->create()->rename($new_id, $new_name);
                }, 
                UsedNameException::class
        );
    }
    
    public function testDelete()
    {
        $data_creator = $this->data_creator_factory->create();
        $warehouse_model = $this->warehouse_model_factory->create();
        $name = 'test_oqixcjhfdskfhkshxyqww';
        $new_warehouse_id = $data_creator->createWarehouse($name);
        $warehouse_model->delete($new_warehouse_id);
        $warehouse = $this->em->getRepository(Warehouse::class)->findOneByName($name);
        Assert::null($warehouse);
        
        $item_in_new_warehouse = $data_creator->createItemInNewWarehouse($name, 'item_mvcklmladsadccvnd', 'lot_dsajhdjkhashhvc');
        $new_warehouse_id_2 = $item_in_new_warehouse['warehouse_id'];
        
        Assert::exception(
                function() use($warehouse_model, $new_warehouse_id_2) {
                    $warehouse_model->delete($new_warehouse_id_2);
                }, 
                NotEmptyException::class
        );
    }
    
    public function testUsedArea()
    {
        $data_creator = $this->data_creator_factory->create();
        $warehouse_model = $this->warehouse_model_factory->create();        
        $item_in_new_warehouse = $data_creator->createItemInNewWarehouse('test_oqixcjhfdskfhkshxyqww', 'item_mvcklmladsadccvnd', 'lot_dsajhdjkhashhvc');
        $new_warehouse_id = $item_in_new_warehouse['warehouse_id'];
        
        $warehouse_list = $warehouse_model->printList();
        $_warehouse = ArrayTools::searchInMultiArray($warehouse_list, $new_warehouse_id, 'id');
        Assert::count(1, $_warehouse);
        $warehouse = reset($_warehouse);
        
        Assert::equal((int)$warehouse['area_filled'], 20);
    }
    
}
