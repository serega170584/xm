<?php

namespace App\Tests;

use App\Entity\CompanyHistory;
use App\Repository\CompanyHistoryRepository;
use App\Service\CompanyHistoryReport;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

class CompnyHistoryReportTest extends KernelTestCase
{
    /**
     * @dataProvider getFields
     */
    public function testSomething(string $symbol, string $startDate, string $endDate, array $expectedLabels, array $expectedOpen, array $expectedClose): void
    {
        $kernel = self::bootKernel();

        $container = static::getContainer();

        /**
         * @var EntityManagerInterface $em
         */
        $em = $container->get('doctrine.orm.entity_manager');

        $report = new CompanyHistoryReport();

        $_GET['symbol'] = $symbol;
        $_GET['start_date'] = $startDate;
        $_GET['end_date'] = $endDate;
        $request = Request::createFromGlobals();

        /**
         * @var CompanyHistoryRepository $repository
         */
        $repository = $em->getRepository(CompanyHistory::class);

        $report->generate($request, $repository);

        $this->assertTrue($expectedLabels === $report->getLabels());
        $this->assertTrue($expectedOpen === $report->getOpenData());
        $this->assertTrue($expectedClose === $report->getCloseData());
    }

    public function getFields()
    {
        yield ['AUG', '2021-03-01', '2021-03-23', ['2021-03-19'], [['x' => '2021-03-19', 'y' => 20]], [['x' => '2021-03-19', 'y' => 55]]];
    }
}
