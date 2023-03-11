<?php

namespace App\Command;

use App\Entity\Company;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:api:company:import',
    description: 'Add a short description for your command',
)]
class ApiCompanyImportCommand extends Command
{
    private const DEFAULT_URL = 'https://pkgstore.datahub.io/core/nasdaq-listings/nasdaq-listed_json/data/a5bc7580d6176d60ac0b2142ca8d7df6/nasdaq-listed_json.json';

    private HttpClientInterface $httpClient;

    private EntityManagerInterface $em;

    public function __construct(HttpClientInterface $httpClient, EntityManagerInterface $em, string $name = null)
    {
        parent::__construct($name);
        $this->httpClient = $httpClient;
        $this->em = $em;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('import_url', InputArgument::OPTIONAL, 'Companies import url')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws TransportExceptionInterface
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws \Exception
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $importUrl = $input->getArgument('import_url');

        if ($importUrl) {
            $io->note(sprintf('You passed an argument: %s', $importUrl));
        }

        $importUrl = NULL === $importUrl ? self::DEFAULT_URL : $importUrl;

        $response = $this->httpClient->request('GET', $importUrl);
        $code = $response->getStatusCode();
        $headers = $response->getHeaders();
        $contentType = $headers['content-type'][0] ?? NULL;

        if (200 !== $code) {
            throw new \Exception('Wrong code status');
        }

        if ('text/plain' !== $contentType) {
            throw new \Exception('Wrong content type');
        }

        $emptyRsm = new \Doctrine\ORM\Query\ResultSetMapping();
        $sql = 'TRUNCATE TABLE company';
        $query = $this->em->createNativeQuery($sql, $emptyRsm);
        $query->execute();

        $apiCompanies = $response->toArray();

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

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
