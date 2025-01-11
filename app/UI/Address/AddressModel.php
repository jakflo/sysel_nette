<?php
namespace App\UI\Address;

use \App\UI\Exceptions\NotFoundException;
use \App\UI\Entities\Address;

class AddressModel
{
    public function __construct(
            protected \Doctrine\ORM\EntityManager $em
    )
    {
        
    }
    
    public function getAddress(int $address_id): Address
    {
        $address = $this->em->getRepository(Address::class)->findOneById($address_id);
        if (!$address) {
            throw new NotFoundException('Adresa nenalezena', NotFoundException::ADDRESS);
        }
        return $address;
    }
}
