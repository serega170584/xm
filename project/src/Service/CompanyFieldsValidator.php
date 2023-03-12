<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\CompanyRepository;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

class CompanyFieldsValidator
{
    private array $errorMessages = [];

    private CompanyRepository $companyRepository;

    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    public function validate(Request $request): void
    {
        $constraint = new Assert\Collection([
            'symbol' => new Assert\NotBlank(),
            'start_date' => [
                new Assert\NotBlank(),
                new Assert\Date()
            ],
            'end_date' => [
                new Assert\NotBlank(),
                new Assert\Date()
            ],
            'email' => [
                new Assert\NotBlank(),
            ]
        ]);

        $values = [
            'symbol' => $request->get('symbol'),
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
            'email' => $request->get('email'),
        ];

        $validator = Validation::createValidator();
        $errors = $validator->validate($values, $constraint);

        $errorMessages = [];

        foreach ($errors as $error) {
            $errorMessages[] = $error->getMessage();
        }

        if ([] === $errorMessages) {
            $startDate = DateTimeImmutable::createFromFormat('Y-m-d', $request->get('start_date'));
            $endDate = DateTimeImmutable::createFromFormat('Y-m-d', $request->get('end_date'));
            $currentDate = new \DateTimeImmutable();

            if ($startDate > $endDate || $endDate > $currentDate) {
                $errorMessages[] = 'Wrong dates';
            }
        }

        if ([] === $errorMessages) {
            $company = $this->companyRepository->findOneBySymbol($request->get('symbol'));

            if (NULL === $company) {
                $errorMessages[] = 'Wrong symbol';
            }
        }

        $this->errorMessages = $errorMessages;
    }

    /**
     * @return array
     */
    public function getErrorMessages(): array
    {
        return $this->errorMessages;
    }
}