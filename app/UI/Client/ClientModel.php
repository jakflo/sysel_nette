<?php
namespace App\UI\Client;
use \App\UI\Entities\Client;
use \App\UI\Exceptions\NotFoundException;

class ClientModel
{
    public function __construct(
            protected \Doctrine\ORM\EntityManager $em, 
            protected \App\UI\Address\AddressModelFactory $address_model_factory
    )
    {
        
    }
    
    public function getClient(int $client_id): Client
    {
        $client = $this->em->getRepository(Client::class)->findOneById($client_id);
        if (!$client) {
            throw new NotFoundException('Klient nenalezen', NotFoundException::CLIENT);
        }
        return $client;
    }
    
    public function create(
            string $forname, 
            string $surname, 
            string|null $middlename, 
            string|null $title, 
            string|null $company_name, 
            string $email, 
            string|null $phone, 
            int $address_id,             
            string|null $note
    ): Client
    {
        $client = new Client();
        $client->setForname($forname)
                ->setSurname($surname)
                ->setMiddlename($middlename)
                ->setTitle($title)
                ->setCompanyName($company_name)
                ->setEmail($email)
                ->setPhone($phone)
                ->setAddress($this->address_model_factory->create()->getAddress($address_id))
                ->setAdded(new \DateTime())
                ->setNote($note)
                ;
        $this->em->persist($client);
        $this->em->flush();
        return $client;
    }
}
