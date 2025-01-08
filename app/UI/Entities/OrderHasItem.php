<?php
namespace App\UI\Entities;

use \Doctrine\ORM\Mapping as ORM;
use \Doctrine\DBAL\Types\Types;
use \App\UI\Entities\Item;
use \App\UI\Entities\Orders;

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
    
    #[ORM\ManyToOne(targetEntity: Orders::class)]
    #[ORM\JoinColumn(name: 'order_id', referencedColumnName: 'id')]
    protected Orders $order;
    
    public function getId(): int
    {
        return $this->id;
    }

    public function getOrderId(): int
    {
        return $this->order_id;
    }

    public function getItemId(): int
    {
        return $this->item_id;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getItem(): Item
    {
        return $this->item;
    }

    public function getOrder(): Orders
    {
        return $this->order;
    }
    
    public function setOrderId(int $order_id)
    {
        $this->order_id = $order_id;
        return $this;
    }

    public function setItemId(int $item_id)
    {
        $this->item_id = $item_id;
        return $this;
    }

    public function setAmount(int $amount)
    {
        $this->amount = $amount;
        return $this;
    }

    public function setItem(Item $item)
    {
        $this->item = $item;
        return $this;
    }

    public function setOrder(Orders $order)
    {
        $this->order = $order;
        return $this;
    }
    
}
