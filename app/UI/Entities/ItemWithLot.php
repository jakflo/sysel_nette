<?php
namespace App\UI\Entities;

use \Doctrine\ORM\Mapping as ORM;
use \Doctrine\DBAL\Types\Types;
use \App\UI\Entities\Item;

#[ORM\Entity]
#[ORM\Table(name: 'item_with_lot')]
class ItemWithLot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    protected int $id;
    
    #[ORM\Column(type: Types::INTEGER)]
    protected int $item_id;
    
    #[ORM\Column(type: Types::STRING)]
    protected string $lot;    
    
    #[ORM\ManyToOne(targetEntity: Item::class)]
    #[ORM\JoinColumn(name: 'item_id', referencedColumnName: 'id')]
    protected Item $item;
    
}
