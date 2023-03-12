<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Company;
use App\Entity\CompanyHistory;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    private HttpClientInterface $httpClient;

    private EntityManagerInterface $em;

    private CompanyRepository $companyRepository;

    public function __construct(HttpClientInterface $httpClient, EntityManagerInterface $em, CompanyRepository $companyRepository, string $name = null)
    {
        parent::__construct($name);
        $this->httpClient = $httpClient;
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
                $response = $this->httpClient->request('GET', $url, [
                    'headers' => [
                        'X-RapidAPI-Key' => '288c19ff5dmsh1077b0b57d92e35p132743jsn307277fc5fa6',
                        'X-RapidAPI-Host' => 'yh-finance.p.rapidapi.com'
                    ]
                ]);

                $code = $response->getStatusCode();
                $headers = $response->getHeaders();
                $contentType = $headers['content-type'][0] ?? NULL;

                if (200 !== $code) {
                    throw new \Exception('Wrong code status');
                }

                if ('application/json' !== $contentType) {
                    throw new \Exception('Wrong content type');
                }

                $prices = $response->toArray();
                $prices = $prices['prices'] ?? [];
                foreach ($prices as $price) {
                    $date = new \DateTimeImmutable();
                    $date->setTimestamp($price['date']);
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
                    $history->setSymbol($symbol);
                    $this->em->persist($history);
                }
                $this->em->flush();
            } catch (\Exception $e) {
                $io->note(sprintf('Exception: %s', $e->getMessage()));
                continue;
            }
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
