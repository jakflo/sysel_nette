<?php
namespace App\UI\Entities;

use \Doctrine\ORM\Mapping as ORM;
use \Doctrine\DBAL\Types\Types;
use \App\UI\Entities\Address;

#[ORM\Entity]
#[ORM\Table(name: 'manufacturer')]
class Manufacturer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    protected int $id;
    
    #[ORM\Column(type: Types::STRING, length: 64)]
    protected string $name;
    
    #[ORM\Column(type: Types::STRING, length: 45)]
    protected string $email;
    
    #[ORM\Column(type: Types::STRING, length: 45, nullable: true)]
    protected string|null $phone;
    
    #[ORM\Column(type: Types::INTEGER)]
    protected string $address_id;
    
    #[ORM\ManyToOne(targetEntity: Address::class)]
    #[ORM\JoinColumn(name: 'address_id', referencedColumnName: 'id')]
    protected Address $address;
    
    public function getId(): int
    {
        return $this->id;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function getEmail(): string
    {
        return $this->email;
    }
    
    public function getPhone(): string
    {
        return $this->phone;
    }
    
    public function getAddressId(): int
    {
        return $this->address_id;
    }
    
    public function getAddress(): Address
    {
        return $this->address;
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
    
    public function setEmail(string $email)
    {
        $this->email = $email;
        return $this;
    }
    
    public function setPhone(string $phone)
    {
        $this->phone = $phone;
        return $this;
    }
    
    public function setAddressId(int $address_id)
    {
        $this->address_id = $address_id;
        return $this;
    }
    
    public function setAddress(Address $address)
    {
        $this->address = $address;
        return $this;
    }
    
}
