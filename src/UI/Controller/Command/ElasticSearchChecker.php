<?php

namespace App\UI\Controller\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ElasticSearchChecker extends Command
{
    protected static $defaultName = 'app:check-travel-elasticsearch';

    protected function configure(): void
    {
        $this->setName('checkElastica')
             ->setDescription('Check ElasticSearch connection (disabled)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('ElasticSearch is disabled.');

        return Command::SUCCESS;
    }
}
