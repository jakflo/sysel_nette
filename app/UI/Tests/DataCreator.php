<?php
namespace App\UI\Tests;

use \App\UI\Entities\ItemStatus;

class DataCreator
{
    public function __construct(
            protected \App\UI\WarehouseList\WarehouseModelFactory $warehouse_model_factory, 
            protected \App\UI\ItemsList\ItemsModelFactory $items_model_factory, 
            protected \App\UI\AddressList\AddressModelFactory $address_model_factory, 
            protected \App\UI\ManufacturerList\ManufacturerModelFactory $manufacturer_model_factory, 
            protected \App\UI\ItemsInWarehouse\ItemsInWarehouseModelFactory $items_in_warehouse_model_factory, 
            protected \Doctrine\ORM\EntityManager $em
    )
    {
        
    }
    
    public function createWarehouse(string $name): int
    {
        return $this->warehouse_model_factory->create()->create($name, 100);
    }
    
    public function createItem(string $name):int
    {
        return $this->items_model_factory->create()->create($name, 2, $this->createManufacturer());
    }
    
    /**     
     * @return array [warehouse_id, item_id]
     */
    public function createItemInNewWarehouse(string $warehouse_name, string $item_name, string $item_lot_name, int $items_amount = 10): array
    {
        $warehouse_id = $this->createWarehouse($warehouse_name);
        $item_id = $this->createItem($item_name);
        $this->items_in_warehouse_model_factory->create()->addItems($warehouse_id, $item_id, $item_lot_name, $items_amount);
        return [
            'warehouse_id' => $warehouse_id, 
            'item_id' => $item_id
        ];
    }
    
    public function createAddress(): int
    {
        return $this->address_model_factory->create()->create('street_dsjhkdhcccwijd', 'city_cccejfjnas', 'country_lwkdasjdahsd');
    }
    
    public function createManufacturer(): int
    {
        return $this->manufacturer_model_factory->create()->create('name_jfkldsjafew', 'email_ddvcowehhv', 'phone_ppscjhebvcswe', $this->createAddress());
    }
    
    /**     
     * @return array taky, jak by to vratila ItemsInWarehouseModel::getList
     */
    public function createTwoItemsInNewWarehouse(string $warehouse_name, string $item_name, bool $few_reserved_items): array
    {
        $warehouse_name_1 = "{$warehouse_name}_1";
        $warehouse_name_2 = "{$warehouse_name}_2";
        $item_name_1 = "{$item_name}_1";
        $item_name_2 = "{$item_name}_2";
        $lot_name_1 = 'lot_fkldsjfkljdsfljwwwwe';
        $lot_name_2 = 'lot_eeoivcxkledsaafdopki';
        
        $inw_1 = $this->createItemInNewWarehouse($warehouse_name_1, $item_name_1, $lot_name_1);
        [$warehouse_id_1, $item_id_1] = [$inw_1['warehouse_id'], $inw_1['item_id']];
        $inw_2 = $this->createItemInNewWarehouse($warehouse_name_2, $item_name_2, $lot_name_2);
        [$warehouse_id_2, $item_id_2] = [$inw_2['warehouse_id'], $inw_2['item_id']];
        
        if ($few_reserved_items) {
            $this->reserveItems($warehouse_id_1, $item_id_1, 5);
        }
        
        $return = [];
        $return[$warehouse_name_1] = [
            0 => [
                'warehouse_id' => $warehouse_id_1, 
                'warehouse' => $warehouse_name_1, 
                'item_id' => $item_id_1, 
                'item' => $item_name_1, 
                'n' => $few_reserved_items ? 5 : 10
            ]
        ];
        $return[$warehouse_name_2] = [
            0 => [
                'warehouse_id' => $warehouse_id_2, 
                'warehouse' => $warehouse_name_2, 
                'item_id' => $item_id_2, 
                'item' => $item_name_2, 
                'n' => 10
            ]
        ];
        
        return $return;
    }
    
    protected function reserveItems(int $warehouse_id, int $item_id, int $amount)
    {
        $items_in_warehouse = $this->em->createQuery(
                "SELECT wi 
                FROM App\\UI\\Entities\\WarehouseHasItem wi 
                JOIN wi.item_with_lot il 
                WHERE wi.warehouse_id = :wid AND il.item_id = :iid"
        )
                ->setParameters(['wid' => $warehouse_id, 'iid' => $item_id])
                ->getResult()
                ;
        
        $status = $this->em->getRepository(ItemStatus::class)->findOneBy(['short_name' => 'reserved']);        
        if (!$items_in_warehouse || $amount > count($items_in_warehouse)) {
            throw new \Exception('nelze rezervovat vic polozek, nez je ulozeno');
        }
        
        for ($c = 0; $c < $amount; $c++) {
            $items_in_warehouse[$c]->setStatus($status);
        }
        
        $this->em->flush();
    }
    
}
