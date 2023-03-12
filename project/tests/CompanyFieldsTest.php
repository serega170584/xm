<?php

namespace App\Tests;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use App\Service\CompanyApiProvider;
use App\Service\CompanyFieldsValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

class CompanyFieldsTest extends KernelTestCase
{
    /**
     * @dataProvider getFields
     */
    public function testValidating(string $symbol, string $startDate, string $endDate, string $email, bool $expectedHasErrors): void
    {
        $_GET['symbol'] = $symbol;
        $_GET['start_date'] = $startDate;
        $_GET['end_date'] = $endDate;
        $_GET['email'] = $email;
        $request = Request::createFromGlobals();

        $container = static::getContainer();

        /**
         * @var EntityManagerInterface $em
         */
        $em = $container->get('doctrine.orm.entity_manager');

        /**
         * @var CompanyRepository $repository
         */
        $repository = $em->getRepository(Company::class);

        $validator = new CompanyFieldsValidator($repository);
        $validator->validate($request);

        $actualHasErrors = (bool)$validator->getErrorMessages();

        $this->assertTrue($actualHasErrors === $expectedHasErrors);
    }

    public function getFields()
    {
        yield ['AGU', '2023-03-06', '2023-03-07', 'test@test.ru', true];
        yield ['AUGD', '2023-03-06', '2023-03-07', 'test@test.ru', false];
        yield ['AUGD', '2023-03-08', '2023-03-07', 'test@test.ru', true];
        yield ['AUGD', '2023-03-08', '2023-03-14', 'test@test.ru', true];
        yield ['AGU', '2023-03-06', '2023-03-07', '', true];
        yield ['AGU', '2023-03-06', '', 'test@test.ru', true];
        yield ['AGU', '', '2023-03-07', 'test@test.ru', true];
        yield ['', '2023-03-06', '2023-03-07', 'test@test.ru', true];
//        yield ['AGU', '2023-03-06', '2023-03-07', 'test@test.ru', true];
    }
}
