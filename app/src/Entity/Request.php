<?php

namespace App\Entity;

use App\Repository\RequestRepository;
use Doctrine\ORM\Mapping as ORM;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

#[ORM\Entity(repositoryClass: RequestRepository::class)]
class Request extends Entity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $ip_address;

    #[ORM\Column(type: 'datetime')]
    private $last_update;

    #[ORM\Column(type: 'integer')]
    private $cached_operations;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIpAddress(): ?string
    {
        return $this->ip_address;
    }

    public function setIpAddress(string $ip_address): self
    {
        $this->ip_address = $ip_address;

        return $this;
    }

    public function getLastUpdate(): ?\DateTimeInterface
    {
        return $this->last_update;
    }

    public function setLastUpdate(\DateTimeInterface $last_update): self
    {
        $this->last_update = $last_update;

        return $this;
    }

    public function getCachedOperations(): ?int
    {
        return $this->cached_operations;
    }

    public function setCachedOperations(int $cached_operations): self
    {
        $this->cached_operations = $cached_operations;

        return $this;
    }
}
