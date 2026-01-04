<?php
namespace App\UI\Entities;

use \Doctrine\ORM\Mapping as ORM;
use \Doctrine\DBAL\Types\Types;

#[ORM\Entity]
#[ORM\Table(name: 'address')]
class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    protected int $id;
    
    #[ORM\Column(type: Types::STRING, length: 64)]
    protected string $street;
    
    #[ORM\Column(type: Types::STRING, length: 45)]
    protected string $city;
    
    #[ORM\Column(type: Types::STRING, length: 10)]
    protected string $zip;
    
    #[ORM\Column(type: Types::STRING, length: 45)]
    protected string $country;
    
    
    public function getId(): int
    {
        return $this->id;
    }
    
    public function getStreet(): string
    {
        return $this->street;
    }
    
    public function getCity(): string
    {
        return $this->city;
    }
    
    public function getCountry(): string
    {
        return $this->country;
    }
    
    public function getZip(): string
    {
        return $this->zip;
    }
    
    public function setId(int $id)
    {
        $this->id = $id;
        return $this;
    }
    
    public function setStreet(string $street)
    {
        $this->street = $street;
        return $this;
    }
    
    public function setCity(string $city)
    {
        $this->city = $city;
        return $this;
    }
    
    public function setCountry(string $country)
    {
        $this->country = $country;
        return $this;
    }
    
    public function setZip(string $zip)
    {
        $this->zip = $zip;
        return $this;
    }
    
}
