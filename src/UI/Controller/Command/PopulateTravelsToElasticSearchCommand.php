<?php

namespace App\UI\Controller\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateTravelsToElasticSearchCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:populate-travel-elasticsearch';

    protected function configure()
    {
        // ...
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // ...
    }
}
