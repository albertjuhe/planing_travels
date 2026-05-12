<?php

namespace App\UI\Command;

use App\Domain\Money\Model\ExchangeRate;
use App\Domain\Money\Service\ExchangeRateProvider;
use App\Infrastructure\Money\Repository\DoctrineExchangeRateRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:rates:refresh', description: 'Refresh exchange rates from Frankfurter API')]
class RefreshExchangeRatesCommand extends Command
{
    private ExchangeRateProvider $provider;
    private DoctrineExchangeRateRepository $rateRepo;

    private array $defaultPairs = [
        ['EUR', 'USD'], ['EUR', 'GBP'], ['EUR', 'JPY'], ['EUR', 'CHF'],
        ['EUR', 'CAD'], ['EUR', 'AUD'], ['EUR', 'SEK'], ['EUR', 'NOK'],
        ['USD', 'EUR'], ['USD', 'GBP'], ['GBP', 'EUR'], ['GBP', 'USD'],
    ];

    public function __construct(ExchangeRateProvider $provider, DoctrineExchangeRateRepository $rateRepo)
    {
        parent::__construct();
        $this->provider = $provider;
        $this->rateRepo = $rateRepo;
    }

    protected function configure(): void
    {
        $this->addOption('pairs', null, InputOption::VALUE_OPTIONAL, 'Comma-separated FROM:TO pairs', null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $today = new \DateTime('today');
        $pairs = $this->defaultPairs;

        if ($pairsOpt = $input->getOption('pairs')) {
            $pairs = array_map(
                fn ($p) => explode(':', trim($p)),
                explode(',', $pairsOpt)
            );
        }

        $refreshed = 0;
        foreach ($pairs as [$from, $to]) {
            try {
                $rate = $this->provider->getRate($from, $to, $today);
                $cached = $this->rateRepo->findFresh($from, $to, $today, 1);
                if ($cached !== null) {
                    $cached->setRate($rate);
                    $this->rateRepo->save($cached);
                } else {
                    $exchangeRate = new ExchangeRate($from, $to, $rate, $today);
                    $this->rateRepo->save($exchangeRate);
                }
                $output->writeln("  {$from}/{$to}: {$rate}");
                $refreshed++;
            } catch (\Throwable $e) {
                $output->writeln("  <error>Failed {$from}/{$to}: {$e->getMessage()}</error>");
            }
        }

        $output->writeln("<info>Refreshed {$refreshed} rates.</info>");

        return Command::SUCCESS;
    }
}
