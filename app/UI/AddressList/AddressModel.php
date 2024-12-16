<?php
namespace App\UI\AddressList;

use \App\UI\Entities\Address;
use \App\UI\Exceptions\NotFoundException;

class AddressModel
{
    public function __construct(
            protected \Doctrine\ORM\EntityManager $em
    )
    {
        
    }
    
    public function create(string $street, string $city, string $country): int
    {
        $address = new Address();
        $address
                ->setStreet($street)
                ->setCity($city)
                ->setCountry($country)
                ;
        $this->em->persist($address);
        $this->em->flush();
        return $address->getId();
    }
    
    public function getAddress(int $address_id): Address
    {
        $address = $this->em->getRepository(Address::class)->findOneById($address_id);
        if (!$address) {
            throw new NotFoundException('adresa nenalezena', NotFoundException::ADDRESS);
        }
        return $address;
    }
    
}
