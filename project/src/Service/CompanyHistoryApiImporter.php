<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Company;
use App\Entity\CompanyHistory;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\DateTimeImmutable;

class CompanyHistoryApiImporter
{
    private CompanyHistoryApiProvider $companyHistoryApiProvider;

    private EntityManagerInterface $em;

    private Company $company;

    public function __construct(CompanyHistoryApiProvider $companyHistoryApiProvider, EntityManagerInterface $em)
    {
        $this->companyHistoryApiProvider = $companyHistoryApiProvider;
        $this->em = $em;
    }

    public function setCompany(Company $company): void
    {
        $this->company = $company;
    }

    public function import(string $importUrl): void
    {
        $prices = $this->companyHistoryApiProvider->getPrices($importUrl);

        foreach ($prices as $price) {
            $date = new \DateTime();
            $date->setTimestamp($price['date']);
            $date = DateTimeImmutable::createFromFormat('Y-m-d', $date->format('Y-m-d'));
            $open = $price['open'] ?? 0;
            $open = (int) ($open * 100);
            $high = $price['high'] ?? 0;
            $high = (int) ($high * 100);
            $low = $price['low'] ?? 0;
            $low = (int) ($low * 100);
            $close = $price['close'] ?? 0;
            $close = (int) ($close * 100);
            $volume = $price['volume'] ?? 0;
            $adjclose = $price['adjclose'] ?? 0;
            $adjclose = (int) ($adjclose * 100);

            $history = new CompanyHistory();
            $history->setDate($date);
            $history->setOpen($open);
            $history->setHigh($high);
            $history->setLow($low);
            $history->setClose($close);
            $history->setVolume($volume);
            $history->setAdjclose($adjclose);
            $history->setSymbol($this->company->getSymbol());
            $this->em->persist($history);
        }
        $this->em->flush();
    }
}