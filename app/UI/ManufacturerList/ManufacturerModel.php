<?php
namespace App\UI\ManufacturerList;

use \App\UI\Entities\Manufacturer;
use \App\UI\Exceptions\NotFoundException;

class ManufacturerModel
{
    public function __construct(
            protected \Doctrine\ORM\EntityManager $em
    )
    {
        
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
