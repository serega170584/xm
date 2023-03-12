<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Company;
use App\Entity\CompanyHistory;
use App\Repository\CompanyRepository;
use App\Service\CompanyHistoryApiImporter;
use App\Service\CompanyHistoryApiProvider;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\DateTimeImmutable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:api:history:import',
    description: 'History import',
)]
class ApiHistoryImportCommand extends Command
{
    private const DEFAULT_URL = 'https://yh-finance.p.rapidapi.com/stock/v3/get-historical-data';

    private EntityManagerInterface $em;

    private CompanyRepository $companyRepository;

    private CompanyHistoryApiImporter $companyHistoryApiImporter;

    public function __construct(CompanyHistoryApiImporter $companyHistoryApiImporter, EntityManagerInterface $em, CompanyRepository $companyRepository, string $name = null)
    {
        parent::__construct($name);
        $this->companyHistoryApiImporter = $companyHistoryApiImporter;
        $this->em = $em;
        $this->companyRepository = $companyRepository;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('import_url', InputArgument::OPTIONAL, 'History import url')
        ;
    }

    /**
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $baseImportUrl = $input->getArgument('import_url');

        if ($baseImportUrl) {
            $io->note(sprintf('You passed an argument: %s', $baseImportUrl));
        }

        $emptyRsm = new \Doctrine\ORM\Query\ResultSetMapping();
        $sql = 'TRUNCATE TABLE company_history';
        $query = $this->em->createNativeQuery($sql, $emptyRsm);
        $query->execute();

        $baseImportUrl = null === $baseImportUrl ? self::DEFAULT_URL : $baseImportUrl;

        $companies = $this->companyRepository->findAllRows();
        /**
         * @var Company $company
         */
        foreach ($companies as $company) {
            $symbol = $company->getSymbol();
            $io->note(sprintf('Import company history with symbol: %s', $symbol));
            $url = $baseImportUrl . '?symbol=' . $symbol;

            try {
                $this->companyHistoryApiImporter->setCompany($company);
                $this->companyHistoryApiImporter->import($url);
            } catch (\Exception $e) {
                $io->note(sprintf('Exception: %s', $e->getMessage()));
                continue;
            }
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
