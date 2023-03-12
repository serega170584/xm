<?php

namespace App\Tests;

use App\Entity\Company;
use App\Entity\CompanyHistory;
use App\Repository\CompanyHistoryRepository;
use App\Service\CompanyHistoryApiImporter;
use App\Service\CompanyHistoryApiProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CompanyHistoryImportTest extends KernelTestCase
{
    /**
     * @throws \Exception
     */
    public function testCompanyHistoryAdding(): void
    {
        self::bootKernel();

        $companyHistoryApiProvider = $this->createMock(CompanyHistoryApiProvider::class);
        $companyHistoryApiProvider->expects($this->once())
                           ->method('getPrices')
                           ->will($this->returnValue([[
                                                         'date' => 1111222333,
                                                         'open' => 1.20,
                                                         'high' => 2.40,
                                                         'low' => 3.40,
                                                         'close' => 4.40,
                                                         'volume' => 500,
                                                         'adjclose' => 1.60
                                                     ]]));

        $company = new Company();
        $company->setSymbol('AAA');

        $container = static::getContainer();

        /**
         * @var EntityManagerInterface $em
         */
        $em = $container->get('doctrine.orm.entity_manager');

        $companyHistoryApiImporter = new CompanyHistoryApiImporter($companyHistoryApiProvider, $em);

        $companyHistoryApiImporter->setCompany($company);

        $companyHistoryApiImporter->import('');

        /**
         * @var CompanyHistoryRepository $repository
         */
        $repository = $em->getRepository(CompanyHistory::class);
        $companyHistory = $repository->findLastOne();

        $this->assertTrue($companyHistory->getDate()->format('Y-m-d') === '2005-03-19');
        $this->assertTrue($companyHistory->getOpen() === 120);
        $this->assertTrue($companyHistory->getHigh() === 240);
        $this->assertTrue($companyHistory->getLow() === 340);
        $this->assertTrue($companyHistory->getClose() === 440);
        $this->assertTrue($companyHistory->getVolume() === 500);
        $this->assertTrue($companyHistory->getAdjclose() === 160);
    }
}
