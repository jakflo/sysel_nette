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
    
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    protected \DateTime $added;
    
    #[ORM\ManyToOne(targetEntity: Item::class)]
    #[ORM\JoinColumn(name: 'item_id', referencedColumnName: 'id')]
    protected Item $item;
    
    public function getId(): int
    {
        return $this->id;
    }
    
    public function getItemId(): int
    {
        return $this->item_id;
    }
    
    public function getLot(): string
    {
        return $this->lot;
    }
    
    public function getAdded(): \DateTime
    {
        return $this->added;
    }
    
    public function getItem(): Item
    {
        return $this->item;
    }
    
    public function setId(int $id)
    {
        $this->id = $id;
        return $this;
    }
    
    public function setItemId(int $item_id)
    {
        $this->item_id = $item_id;
        return $this;
    }
    
    public function setLot(string $lot)
    {
        $this->lot = $lot;
        return $this;
    }
    
    public function setAdded(\DateTime $added)
    {
        $this->added = $added;
        return $this;
    }
    
    public function setItem(Item $item)
    {
        $this->item = $item;
        return $this;
    }
    
}
