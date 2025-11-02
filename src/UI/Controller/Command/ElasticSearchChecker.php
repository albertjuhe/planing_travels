<?php

namespace App\UI\Controller\Command;

use Elastica\Client;
use http\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ElasticSearchChecker extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:check-travel-elasticsearch';
    private $client;

    public function __construct(Client $client, string $name = null)
    {
        parent::__construct($name);

        $this->client = $client;
    }

    protected function configure()
    {
        $this->setName('checkElastica');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $output->writeln('checking elasticsearch');
            $status = $this->client->getStatus();
            $output->writeln('elasticsearch exists'.var_dump($status->getData()));
            $this->client->getIndex('travel');
        } catch (Exception $e) {
            $output->writeln('elasticsearch not exists'.$e->getMessage());

            return false;
        }

        return true;
    }
}
