<?php
namespace App\UI\Entities;

use \Doctrine\ORM\Mapping as ORM;
use \Doctrine\DBAL\Types\Types;
use \App\UI\Entities\ItemStatus;
use \App\UI\Entities\ItemWithLot;
use \App\UI\Entities\Warehouse;

#[ORM\Entity]
#[ORM\Table(name: 'warehouse_has_item')]
class WarehouseHasItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    protected int $id;
    
    #[ORM\Column(type: Types::INTEGER)]
    protected int $warehouse_id;
    
    #[ORM\Column(type: Types::INTEGER)]
    protected int $item_with_lot_id;
    
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    protected \DateTime $added;
    
    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    protected int|null $order_id;
    
    #[ORM\Column(type: Types::INTEGER)]
    protected int $status_id;
    
    #[ORM\ManyToOne(targetEntity: Warehouse::class)]
    #[ORM\JoinColumn(name: 'warehouse_id', referencedColumnName: 'id')]
    protected Warehouse $warehouse;
    
    #[ORM\ManyToOne(targetEntity: ItemStatus::class)]
    #[ORM\JoinColumn(name: 'status_id', referencedColumnName: 'id')]
    protected ItemStatus $status;
    
    #[ORM\ManyToOne(targetEntity: ItemWithLot::class)]
    #[ORM\JoinColumn(name: 'item_with_lot_id', referencedColumnName: 'id')]
    protected ItemWithLot $item_with_lot;
    
    public function getId(): int
    {
        return $this->id;
    }
    
    public function getWarehouseId(): int
    {
        return $this->warehouse_id;
    }
    
    public function getItemWithLotId(): int
    {
        return $this->item_with_lot_id;
    }
    
    public function getAdded(): \DateTime
    {
        return $this->added;
    }
    
    public function getOrderId(): int | null
    {
        return $this->order_id;
    }
    
    public function getStatusId(): int
    {
        return $this->status_id;
    }
    
    public function getWarehouse(): Warehouse
    {
        return $this->warehouse;
    }
    
    public function getStatus(): ItemStatus
    {
        return $this->status;
    }
    
    public function getItemWithLot(): ItemWithLot
    {
        return $this->item_with_lot;
    }
    
    public function setId(int $id)
    {
        $this->id = $id;
        return $this;
    }
    
    public function setWarehouseId(int $warehouse_id)
    {
        $this->warehouse_id = $warehouse_id;
        return $this;
    }
    
    public function setItemWithLotId(int $item_with_lot_id)
    {
        $this->item_with_lot_id = $item_with_lot_id;
        return $this;
    }
    
    public function setAdded(\DateTime $added)
    {
        $this->added = $added;
        return $this;
    }
    
    public function setOrderId(int | null $order_id)
    {
        $this->order_id = $order_id;
        return $this;
    }
    
    public function setStatusId(int $status_id)
    {
        $this->status_id = $status_id;
        return $this;
    }
    
    public function setWarehouse(Warehouse $warehouse)
    {
        $this->warehouse = $warehouse;
        return $this;
    }
    
    public function setStatus(ItemStatus $status)
    {
        $this->status = $status;
        return $this;
    }
    
    public function setItemWithLot(ItemWithLot $item_with_lot)
    {
        $this->item_with_lot = $item_with_lot;
        return $this;
    }
    
}
