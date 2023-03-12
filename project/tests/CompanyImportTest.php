<?php

namespace App\Tests;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use App\Service\CompanyApiImporter;
use App\Service\CompanyApiProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CompanyImportTest extends KernelTestCase
{
    /**
     * @throws \Exception
     */
    public function testCompanyAdding(): void
    {
        self::bootKernel();

        $companyApiProvider = $this->createMock(CompanyApiProvider::class);
        $companyApiProvider->expects($this->once())
                 ->method('getCompanies')
                 ->will($this->returnValue([[
                     'Company Name' => '123',
                     'Financial Status' => '5',
                     'Market Category' => '3',
                     'Round Lot Size' => 345.00,
                     'Security Name' => '321',
                     'Symbol' => '987',
                     'Test Issue' => '5'
                 ]]));

        $container = static::getContainer();

        /**
         * @var EntityManagerInterface $em
         */
        $em = $container->get('doctrine.orm.entity_manager');

        $companyApiImporter = new CompanyApiImporter($companyApiProvider, $em);

        $companyApiImporter->import('');

        /**
         * @var CompanyRepository $repository
         */
        $repository = $em->getRepository(Company::class);
        $company = $repository->findLastOne();

        $this->assertTrue($company->getCompanyName() === '123');
        $this->assertTrue($company->getFinancialStatus() === '5');
        $this->assertTrue($company->getMarketCategory() === '3');
        $this->assertTrue($company->getRoundLotSize() === 345.00);
        $this->assertTrue($company->getSecurityName() === '321');
        $this->assertTrue($company->getSymbol() === '987');
        $this->assertTrue($company->getTestIssue() === '5');
    }
}
