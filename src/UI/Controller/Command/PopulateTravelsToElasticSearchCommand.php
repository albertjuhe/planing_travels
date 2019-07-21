<?php

namespace App\UI\Controller\Command;

use App\Application\UseCases\Travel\PopulateIndexer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateTravelsToElasticSearchCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:populate-travel-elasticsearch';
    /**
     * @var PopulateIndexer
     */
    private $populateIndexer;

    public function __construct(PopulateIndexer $populateIndexer, string $name = null)
    {
        parent::__construct($name);
        $this->populateIndexer = $populateIndexer;
    }

    protected function configure()
    {
        // ...
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->populateIndexer->execute();
    }
}
