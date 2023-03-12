<?php

namespace App\DataFixtures;

use App\Entity\Company;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CompanyFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $company = new Company();
        $company->setSymbol('AUGD');
        $company->setFinancialStatus('3');
        $company->setMarketCategory('4');
        $company->setRoundLotSize(600.00);
        $company->setSecurityName('321');
        $company->setCompanyName('Test');
        $company->setTestIssue('4');
        $manager->persist($company);

        $company = new Company();
        $company->setSymbol('DUGD');
        $company->setFinancialStatus('5');
        $company->setMarketCategory('6');
        $company->setRoundLotSize(300.00);
        $company->setSecurityName('421');
        $company->setCompanyName('Test111');
        $company->setTestIssue('5');
        $manager->persist($company);

        $company = new Company();
        $company->setSymbol('VUDD');
        $company->setFinancialStatus('9');
        $company->setMarketCategory('8');
        $company->setRoundLotSize(500.00);
        $company->setSecurityName('521');
        $company->setCompanyName('Test2');
        $company->setTestIssue('6');
        $manager->persist($company);

        $manager->flush();
    }
}
