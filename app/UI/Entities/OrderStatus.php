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
    
}
