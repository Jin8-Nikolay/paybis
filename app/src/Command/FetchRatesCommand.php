<?php

namespace App\Command;

use App\Entity\Rate;
use App\Enum\CryptoPair;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;
use ValueError;

#[AsCommand(
    name: 'app:fetch-rates',
    description: 'Fetches crypto pair rates from Binance API and saves to DB'
)]
class FetchRatesCommand extends Command
{
    private HttpClientInterface $httpClient;
    private EntityManagerInterface $em;
    private LoggerInterface $logger;

    public function __construct(HttpClientInterface $httpClient, EntityManagerInterface $em, LoggerInterface $logger)
    {
        parent::__construct();
        $this->httpClient = $httpClient;
        $this->em = $em;
        $this->logger = $logger;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $url = 'https://api.binance.com/api/v3/ticker/price?symbols=' . urlencode(json_encode(CryptoPair::values()));

        try {
            $response = $this->httpClient->request('GET', $url);

            foreach ($response->toArray() as $item) {
                $pair = $item['symbol'];
                $price = $item['price'];

                try {
                    $pairEnum = CryptoPair::from($pair);
                } catch (ValueError) {
                    $this->logger->warning('Unknown symbol skipped: ' . $pair);
                    continue;
                }

                $rate = new Rate();
                $rate->setPair($pairEnum);
                $rate->setPrice($price);

                $this->em->persist($rate);
            }

            $this->em->flush();

            return Command::SUCCESS;
        } catch (Throwable $e) {
            $this->logger->error('FetchRatesCommand error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
