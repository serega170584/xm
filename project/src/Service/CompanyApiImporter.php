<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Company;
use Doctrine\ORM\EntityManagerInterface;

class CompanyApiImporter
{
    private CompanyApiProvider $companyApiProvider;

    private EntityManagerInterface $em;

    public function __construct(CompanyApiProvider $companyApiProvider, EntityManagerInterface $em)
    {
        $this->companyApiProvider = $companyApiProvider;
        $this->em = $em;
    }

    public function import(string $importUrl): void
    {
        $apiCompanies = $this->companyApiProvider->getCompanies($importUrl);

        foreach ($apiCompanies as $apiCompany) {
            $company = new Company();
            $company->setCompanyName($apiCompany['Company Name']);
            $company->setFinancialStatus($apiCompany['Financial Status']);
            $company->setMarketCategory($apiCompany['Market Category']);
            $company->setRoundLotSize($apiCompany['Round Lot Size']);
            $company->setSecurityName($apiCompany['Security Name']);
            $company->setSymbol($apiCompany['Symbol']);
            $company->setTestIssue($apiCompany['Test Issue']);
            $this->em->persist($company);
        }
        $this->em->flush();
    }
}