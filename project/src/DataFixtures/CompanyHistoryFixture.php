<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\CompanyHistory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CompanyHistoryFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $companyHistory = new CompanyHistory();
        $companyHistory->setDate(new \DateTimeImmutable('2020-03-19'));
        $companyHistory->setOpen(10);
        $companyHistory->setHigh(20);
        $companyHistory->setLow(30);
        $companyHistory->setClose(40);
        $companyHistory->setVolume(50);
        $companyHistory->setAdjclose(60);
        $companyHistory->setSymbol('AUG');
        $manager->persist($companyHistory);

        $companyHistory = new CompanyHistory();
        $companyHistory->setDate(new \DateTimeImmutable('2021-03-19'));
        $companyHistory->setOpen(20);
        $companyHistory->setHigh(30);
        $companyHistory->setLow(40);
        $companyHistory->setClose(55);
        $companyHistory->setVolume(65);
        $companyHistory->setAdjclose(76);
        $companyHistory->setSymbol('AUG');
        $manager->persist($companyHistory);

        $companyHistory = new CompanyHistory();
        $companyHistory->setDate(new \DateTimeImmutable('2022-03-19'));
        $companyHistory->setOpen(220);
        $companyHistory->setHigh(320);
        $companyHistory->setLow(420);
        $companyHistory->setClose(525);
        $companyHistory->setVolume(625);
        $companyHistory->setAdjclose(726);
        $companyHistory->setSymbol('AUG');
        $manager->persist($companyHistory);

        $manager->flush();
    }
}
