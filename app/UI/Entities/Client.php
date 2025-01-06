<?php
namespace App\UI\Entities;

use \Doctrine\ORM\Mapping as ORM;
use \Doctrine\DBAL\Types\Types;
use \App\UI\Entities\Address;

#[ORM\Entity]
#[ORM\Table(name: 'client')]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    protected int $id;
    
    #[ORM\Column(type: Types::STRING)]
    protected string $company_name;
    
    #[ORM\Column(type: Types::STRING)]
    protected string $forname;
    
    #[ORM\Column(type: Types::STRING)]
    protected string $surname;
    
    #[ORM\Column(type: Types::STRING)]
    protected string $middlename;
    
    #[ORM\Column(type: Types::STRING)]
    protected string $title;
    
    #[ORM\Column(type: Types::STRING)]
    protected string $email;
    
    #[ORM\Column(type: Types::STRING)]
    protected string $phone;
    
    #[ORM\Column(type: Types::INTEGER)]
    protected int $address_id;
    
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    protected \DateTime $added;
    
    #[ORM\Column(type: Types::STRING)]
    protected int $note;
    
    #[ORM\ManyToOne(targetEntity: Address::class)]
    #[ORM\JoinColumn(name: 'address_id', referencedColumnName: 'id')]
    protected Address $address;
    
}
