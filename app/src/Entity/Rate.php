<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\CryptoPair;
use App\Repository\RateRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: RateRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'rates')]
class Rate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, enumType: CryptoPair::class)]
    #[Groups(['api'])]
    private CryptoPair $pair;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 8)]
    #[Groups(['api'])]
    private string $price;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['api'])]
    private DateTimeImmutable $createdAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPair(): CryptoPair
    {
        return $this->pair;
    }

    public function setPair(CryptoPair $pair): self
    {
        $this->pair = $pair;
        return $this;
    }

    public function getPrice(): string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        if (!isset($this->createdAt)) {
            $this->createdAt = new DateTimeImmutable();
        }
    }
}
