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
    
    #[ORM\Column(type: Types::STRING)]
    protected string $name;
    
    #[ORM\Column(type: Types::INTEGER)]
    protected int $area;
    
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    protected \DateTime $created;
    
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    protected \DateTime|null $last_edited;    
    
}
