<?php
namespace App\UI\ManufacturerList;

use \App\UI\Entities\Manufacturer;
use \App\UI\Exceptions\NotFoundException;

class ManufacturerModel
{
    public function __construct(
            protected \Doctrine\ORM\EntityManager $em, 
            protected \App\UI\AddressList\AddressModelFactory $address_model_factory
    )
    {
        
    }
    
    public function create(string $name, string $email, string $phone, int $address_id): int
    {
        $address = $this->address_model_factory->create()->getAddress($address_id);
        $manufacturer = new Manufacturer();
        $manufacturer
                ->setName($name)
                ->setEmail($email)
                ->setPhone($phone)
                ->setAddress($address)
                ;
        $this->em->persist($manufacturer);
        $this->em->flush();
        return $manufacturer->getId();
    }
    
    public function getListForSelect(): array
    {
        $manufacturers = $this->em->getRepository(Manufacturer::class)->findAll();
        $result = [];
        
        foreach ($manufacturers as $manufacturer) {
            $address = $manufacturer->getAddress();
            $this->em->initializeObject($address);
            $result[$manufacturer->getId()] = "{$manufacturer->getName()} ({$address->getStreet()}, {$address->getCity()}, {$address->getCountry()})";
        }
        return $result;
    }
    
    public function getManufacturer(int $id): Manufacturer
    {
        $manufacturer = $this->em->getRepository(Manufacturer::class)->findOneById($id);
        if (!$manufacturer) {
            throw new NotFoundException('vyrobce nenalezen', NotFoundException::MANUFACTURER);
        }
        return $manufacturer;
    }
    
}
