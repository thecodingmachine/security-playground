<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CouponRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: CouponRepository::class)]
#[UniqueEntity(fields: ['code'])]
class Coupon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'code', type: 'string', length: 255, unique: true, nullable: true)]
    private ?string $code = null;

    #[ORM\Column(name: 'percent', type: 'integer', nullable: false)]
    private int $percent;

    #[ORM\Column(name: 'expiration_date', type: 'date', nullable: true)]
    private ?DateTime $expirationDate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    public function getPercent(): int
    {
        return $this->percent;
    }

    public function setPercent(int $percent): void
    {
        $this->percent = $percent;
    }

    public function getExpirationDate(): ?DateTime
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(?DateTime $expirationDate): void
    {
        $this->expirationDate = $expirationDate;
    }
}
