<?php
namespace App\UI\Entities;

use \Doctrine\ORM\Mapping as ORM;
use \Doctrine\DBAL\Types\Types;

#[ORM\Entity]
#[ORM\Table(name: 'order_status')]
class OrderStatus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    protected int $id;
    
    #[ORM\Column(type: Types::STRING)]
    protected string $short_name;
    
    #[ORM\Column(type: Types::STRING)]
    protected string $name;
    
    public function getShortName(): string
    {
        return $this->short_name;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function setShortName(string $short_name)
    {
        $this->short_name = $short_name;
        return $this;
    }
    
    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }
    
}
