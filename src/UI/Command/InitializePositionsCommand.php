<?php

namespace App\UI\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Infrastructure\LocationBundle\Repository\DoctrineLocationRepository;

class InitializePositionsCommand extends Command
{
    protected static $defaultName = 'app:initialize-positions';
    private $locationRepository;
    private $em;

    public function __construct(DoctrineLocationRepository $locationRepository, EntityManagerInterface $em)
    {
        parent::__construct();
        $this->locationRepository = $locationRepository;
        $this->em = $em;
    }

    protected function configure()
    {
        $this->setDescription('Initialize position field for existing location_visit_date records');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        
        $conn = $this->em->getConnection();
        
        // Get all location_visit_date records with NULL position
        $records = $conn->fetchAllAssociative('
            SELECT lvd.id, lvd.visit_date, l.travel_id
            FROM location_visit_date lvd
            INNER JOIN location l ON lvd.location_id = l.id
            WHERE lvd.position IS NULL
            ORDER BY l.travel_id, lvd.visit_date, lvd.id
        ');
        
        if (empty($records)) {
            $io->success('All records already have positions initialized.');
            return Command::SUCCESS;
        }
        
        $io->note(sprintf('Found %d records with NULL position', count($records)));
        
        // Group by travel_id and visit_date to assign sequential positions
        $grouped = [];
        foreach ($records as $record) {
            $key = $record['travel_id'] . '_' . $record['visit_date'];
            if (!isset($grouped[$key])) {
                $grouped[$key] = [];
            }
            $grouped[$key][] = $record;
        }
        
        // Update positions
        $conn->beginTransaction();
        try {
            foreach ($grouped as $group) {
                foreach ($group as $index => $record) {
                    $conn->executeStatement(
                        'UPDATE location_visit_date SET position = ? WHERE id = ?',
                        [$index, $record['id']]
                    );
                }
            }
            $conn->commit();
            $io->success(sprintf('Successfully initialized positions for %d records', count($records)));
        } catch (\Exception $e) {
            $conn->rollBack();
            $io->error('Failed to initialize positions: ' . $e->getMessage());
            return Command::FAILURE;
        }
        
        return Command::SUCCESS;
    }
}
