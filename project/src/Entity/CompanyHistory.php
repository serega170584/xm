<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CompanyHistoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompanyHistoryRepository::class)]
class CompanyHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date = null;

    #[ORM\Column]
    private ?int $open = null;

    #[ORM\Column]
    private ?int $high = null;

    #[ORM\Column]
    private ?int $low = null;

    #[ORM\Column]
    private ?int $close = null;

    #[ORM\Column]
    private ?int $volume = null;

    #[ORM\Column]
    private ?int $adjclose = null;

    #[ORM\Column]
    private ?string $symbol = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getOpen(): ?int
    {
        return $this->open;
    }

    public function setOpen(int $open): self
    {
        $this->open = $open;

        return $this;
    }

    public function getHigh(): ?int
    {
        return $this->high;
    }

    public function setHigh(int $high): self
    {
        $this->high = $high;

        return $this;
    }

    public function getLow(): ?int
    {
        return $this->low;
    }

    public function setLow(int $low): self
    {
        $this->low = $low;

        return $this;
    }

    public function getClose(): ?int
    {
        return $this->close;
    }

    public function setClose(int $close): self
    {
        $this->close = $close;

        return $this;
    }

    public function getVolume(): ?int
    {
        return $this->volume;
    }

    public function setVolume(int $volume): self
    {
        $this->volume = $volume;

        return $this;
    }

    public function getAdjclose(): ?int
    {
        return $this->adjclose;
    }

    public function setAdjclose(int $adjclose): self
    {
        $this->adjclose = $adjclose;

        return $this;
    }

    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    public function setSymbol(string $symbol): self
    {
        $this->symbol = $symbol;

        return $this;
    }
}
