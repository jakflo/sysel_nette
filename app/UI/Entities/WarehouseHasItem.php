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
    
    #[ORM\Column(type: Types::INTEGER)]
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
    
}
