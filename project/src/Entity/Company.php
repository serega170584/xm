<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $companyName = null;

    #[ORM\Column(length: 1)]
    private ?string $financialStatus = null;

    #[ORM\Column(length: 1)]
    private ?string $marketCategory = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private ?float $roundLotSize = null;

    #[ORM\Column(length: 255)]
    private ?string $securityName = null;

    #[ORM\Column(length: 4)]
    private ?string $symbol = null;

    #[ORM\Column(length: 1)]
    private ?string $testIssue = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(string $companyName): self
    {
        $this->companyName = $companyName;

        return $this;
    }

    public function getFinancialStatus(): ?string
    {
        return $this->financialStatus;
    }

    public function setFinancialStatus(string $financialStatus): self
    {
        $this->financialStatus = $financialStatus;

        return $this;
    }

    public function getMarketCategory(): ?string
    {
        return $this->marketCategory;
    }

    public function setMarketCategory(string $marketCategory): self
    {
        $this->marketCategory = $marketCategory;

        return $this;
    }

    public function getRoundLotSize(): ?string
    {
        return $this->roundLotSize;
    }

    public function setRoundLotSize(float $roundLotSize): self
    {
        $this->roundLotSize = $roundLotSize;

        return $this;
    }

    public function getSecurityName(): ?string
    {
        return $this->securityName;
    }

    public function setSecurityName(string $securityName): self
    {
        $this->securityName = $securityName;

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

    public function getTestIssue(): ?string
    {
        return $this->testIssue;
    }

    public function setTestIssue(string $testIssue): self
    {
        $this->testIssue = $testIssue;

        return $this;
    }
}
