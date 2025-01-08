<?php
namespace App\UI\Client;
use \App\UI\Entities\Client;
use \App\UI\Exceptions\NotFoundException;

class ClientModel
{
    public function __construct(
            protected \Doctrine\ORM\EntityManager $em
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
}
