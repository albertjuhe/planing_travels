<?php

namespace App\UI\Command;

use App\Domain\Gpx\Model\Gpx;
use App\Infrastructure\GpxBundle\Service\GpxSimplifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(name: 'app:gpx:backfill-distance', description: 'Compute and store distance for existing GPX tracks')]
class BackfillGpxDistanceCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private GpxSimplifier $simplifier,
        #[Autowire('%kernel.project_dir%')]
        private string $projectDir
    ) {
        parent::__construct();
    }

    public static function configureFromContainer(string $projectDir): self
    {
        throw new \LogicException('Use the autowired constructor');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $repo = $this->em->getRepository(Gpx::class);
        $tracks = $repo->createQueryBuilder('g')
            ->where('g.distance IS NULL')
            ->getQuery()->getResult();

        if (!$tracks) {
            $io->success('No tracks need backfilling.');
            return Command::SUCCESS;
        }

        $uploadDir = rtrim($this->projectDir, '/').'/public/uploads/gpx/';
        $count = 0;
        foreach ($tracks as $gpx) {
            /** @var Gpx $gpx */
            $path = $uploadDir.$gpx->getFilename();
            if (!is_file($path)) {
                $io->writeln('<comment>Skip (file missing):</comment> '.$gpx->getFilename());
                continue;
            }
            $meters = $this->simplifier->computeDistance($path);
            $gpx->setDistance($meters);
            $count++;
            $io->writeln(sprintf('  #%d %s → %.2f km', $gpx->getId(), $gpx->getFilename(), $meters / 1000));
        }
        $this->em->flush();

        $io->success(sprintf('Updated %d track(s).', $count));
        return Command::SUCCESS;
    }
}
