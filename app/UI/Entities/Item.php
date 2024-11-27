<?php
namespace App\UI\Entities;

use \Doctrine\ORM\Mapping as ORM;
use \Doctrine\DBAL\Types\Types;
use \App\UI\Entities\Manufacturer;

#[ORM\Entity]
#[ORM\Table(name: 'item')]
class Item
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    protected int $id;
    
    #[ORM\Column(type: Types::STRING)]
    protected string $name;
    
    #[ORM\Column(type: Types::FLOAT)]
    protected string $area;
    
    #[ORM\Column(type: Types::INTEGER)]
    protected string $manufacturer_id;
    
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    protected \DateTime $added;
    
    #[ORM\ManyToOne(targetEntity: Manufacturer::class)]
    #[ORM\JoinColumn(name: 'manufacturer_id', referencedColumnName: 'id')]
    protected Manufacturer $manufacturer;
    
    
    public function getId(): int
    {
        return $this->id;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function getArea(): float
    {
        return $this->area;
    }
    
    public function getAdded(): \DateTime
    {
        return $this->added;
    }
    
    public function getManufacturer(): Manufacturer
    {
        return $this->manufacturer;
    }
    
    public function setId(int $id)
    {
        $this->id = $id;
        return $this;
    }
    
    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }
    
    public function setArea(float $area)
    {
        $this->area = $area;
        return $this;
    }
    
    public function setAdded(\DateTime $added)
    {
        $this->added = $added;
        return $this;
    }
    
    public function setManufacturer(Manufacturer $manufacturer)
    {
        $this->manufacturer = $manufacturer;
        return $this;
    }
    
}
