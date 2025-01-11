<?php
namespace App\UI\Entities;

use \Doctrine\ORM\Mapping as ORM;
use \Doctrine\DBAL\Types\Types;
use \App\UI\Entities\OrderStatus;
use \App\UI\Entities\Client;

#[ORM\Entity]
#[ORM\Table(name: 'orders')]
class Orders
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    protected int $id;
    
    #[ORM\Column(type: Types::INTEGER)]
    protected int $client_id;
    
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    protected \DateTime $added;
    
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    protected \DateTime $last_edited;
    
    #[ORM\Column(type: Types::STRING)]
    protected string|null $note;
    
    #[ORM\Column(type: Types::INTEGER)]
    protected string $status_id;
    
    #[ORM\ManyToOne(targetEntity: OrderStatus::class)]
    #[ORM\JoinColumn(name: 'status_id', referencedColumnName: 'id')]
    protected OrderStatus $status;
    
    #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(name: 'client_id', referencedColumnName: 'id')]
    protected Client $client;
    
    public function getId(): int
    {
        return $this->id;
    }

    public function getClientId(): int
    {
        return $this->client_id;
    }

    public function getAdded(): \DateTime
    {
        return $this->added;
    }

    public function getLastEdited(): \DateTime
    {
        return $this->last_edited;
    }

    public function getNote(): string|null
    {
        return $this->note;
    }

    public function getStatusId(): string
    {
        return $this->status_id;
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setClientId(int $client_id)
    {
        $this->client_id = $client_id;
        return $this;
    }

    public function setAdded(\DateTime $added)
    {
        $this->added = $added;
        return $this;
    }

    public function setLastEdited(\DateTime $last_edited)
    {
        $this->last_edited = $last_edited;
        return $this;
    }

    public function setNote(string|null $note)
    {
        $this->note = $note;
        return $this;
    }

    public function setStatusId(string $status_id)
    {
        $this->status_id = $status_id;
        return $this;
    }

    public function setStatus(OrderStatus $status)
    {
        $this->status = $status;
        return $this;
    }

    public function setClient(Client $client)
    {
        $this->client = $client;
        return $this;
    }
    
}
