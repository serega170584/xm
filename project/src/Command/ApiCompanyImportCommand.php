<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Company;
use App\Service\CompanyApiImporter;
use App\Service\CompanyApiProvider;
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

#[AsCommand(
    name: 'app:api:company:import',
    description: 'Companies import',
)]
class ApiCompanyImportCommand extends Command
{
    private const DEFAULT_URL = 'https://pkgstore.datahub.io/core/nasdaq-listings/nasdaq-listed_json/data/a5bc7580d6176d60ac0b2142ca8d7df6/nasdaq-listed_json.json';

    private CompanyApiImporter $companyApiImporter;

    private EntityManagerInterface $em;

    public function __construct(CompanyApiImporter $companyApiImporter, EntityManagerInterface $em, string $name = null)
    {
        parent::__construct($name);
        $this->companyApiImporter = $companyApiImporter;
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

        try {
            $emptyRsm = new \Doctrine\ORM\Query\ResultSetMapping();
            $sql = 'TRUNCATE TABLE company';
            $query = $this->em->createNativeQuery($sql, $emptyRsm);
            $query->execute();

            $this->companyApiImporter->import($importUrl);

        } catch (\Exception $e) {
            $io->note(sprintf('Exception: %s', $e->getMessage()));
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
