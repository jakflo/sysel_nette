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
    
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true)]
    protected string|null $company_name;
    
    #[ORM\Column(type: Types::STRING, length: 45)]
    protected string $forname;
    
    #[ORM\Column(type: Types::STRING, length: 45)]
    protected string $surname;
    
    #[ORM\Column(type: Types::STRING, length: 45, nullable: true)]
    protected string|null $middlename;
    
    #[ORM\Column(type: Types::STRING, length: 45, nullable: true)]
    protected string|null $title;
    
    #[ORM\Column(type: Types::STRING, length: 45)]
    protected string $email;
    
    #[ORM\Column(type: Types::STRING, length: 45, nullable: true)]
    protected string|null $phone;
    
    #[ORM\Column(type: Types::INTEGER)]
    protected int $address_id;
    
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    protected \DateTime $added;
    
    #[ORM\Column(type: Types::STRING, length: 128, nullable: true)]
    protected string|null $note;
    
    #[ORM\ManyToOne(targetEntity: Address::class)]
    #[ORM\JoinColumn(name: 'address_id', referencedColumnName: 'id')]
    protected Address $address;
    
    public function getId(): int
    {
        return $this->id;
    }

    public function getCompanyName(): string|null
    {
        return $this->company_name;
    }

    public function getForname(): string
    {
        return $this->forname;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }

    public function getMiddlename(): string|null
    {
        return $this->middlename;
    }

    public function getTitle(): string|null
    {
        return $this->title;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhone(): string|null
    {
        return $this->phone;
    }

    public function getAddressId(): int
    {
        return $this->address_id;
    }

    public function getAdded(): \DateTime
    {
        return $this->added;
    }

    public function getNote(): string|null
    {
        return $this->note;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function setCompanyName(string|null $company_name)
    {
        $this->company_name = $company_name;
        return $this;
    }

    public function setForname(string $forname)
    {
        $this->forname = $forname;
        return $this;
    }

    public function setSurname(string $surname)
    {
        $this->surname = $surname;
        return $this;
    }

    public function setMiddlename(string|null $middlename)
    {
        $this->middlename = $middlename;
        return $this;
    }

    public function setTitle(string|null $title)
    {
        $this->title = $title;
        return $this;
    }

    public function setEmail(string $email)
    {
        $this->email = $email;
        return $this;
    }

    public function setPhone(string|null $phone)
    {
        $this->phone = $phone;
        return $this;
    }

    public function setAddressId(int $address_id)
    {
        $this->address_id = $address_id;
        return $this;
    }

    public function setAdded(\DateTime $added)
    {
        $this->added = $added;
        return $this;
    }

    public function setNote(string|null $note)
    {
        $this->note = $note;
        return $this;
    }

    public function setAddress(Address $address)
    {
        $this->address = $address;
        return $this;
    }
    
}
