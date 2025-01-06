<?php
namespace App\UI\Entities;

use \Doctrine\ORM\Mapping as ORM;
use \Doctrine\DBAL\Types\Types;
use \App\UI\Entities\Item;

#[ORM\Entity]
#[ORM\Table(name: 'order_has_item')]
class OrderHasItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    protected int $id;
    
    #[ORM\Column(type: Types::INTEGER)]
    protected int $order_id;
    
    #[ORM\Column(type: Types::INTEGER)]
    protected int $item_id;
    
    #[ORM\Column(type: Types::INTEGER)]
    protected int $amount;
    
    #[ORM\ManyToOne(targetEntity: Item::class)]
    #[ORM\JoinColumn(name: 'item_id', referencedColumnName: 'id')]
    protected Item $item;
    
}
