<?php
namespace App\UI\Entities;

use \Doctrine\ORM\Mapping as ORM;
use \Doctrine\DBAL\Types\Types;

#[ORM\Entity]
#[ORM\Table(name: 'warehouse')]
class Warehouse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    protected int $id;
    
    #[ORM\Column(type: Types::STRING, length: 64)]
    protected string $name;
    
    #[ORM\Column(type: Types::INTEGER)]
    protected int $area;
    
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    protected \DateTime $created;
    
    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    protected \DateTime | null $last_edited;

    public function getId(): int
    {
        return $this->id;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function getArea(): int
    {
        return $this->area;
    }
    
    public function getCreated(): \DateTime
    {
        return $this->created;
    }
    
    public function getLastEdited(): \DateTime|null
    {
        return $this->last_edited;
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
    
    public function setArea(int $area)
    {
        $this->area = $area;
        return $this;
    }
    
    public function setCreated(\DateTime $created)
    {
        $this->created = $created;
        return $this;
    }
    
    public function setLastEdited(\DateTime|null $last_edited)
    {
        $this->last_edited = $last_edited;
        return $this;
    }
    
}
