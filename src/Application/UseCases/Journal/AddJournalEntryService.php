<?php

namespace App\Application\UseCases\Journal;

use App\Application\Command\Journal\AddJournalEntryCommand;
use App\Application\UseCases\UsesCasesService;
use App\Domain\Journal\Events\JournalEntryWasCreated;
use App\Domain\Journal\Model\JournalEntry;
use App\Domain\Journal\Repository\JournalEntryRepository;
use App\Domain\Travel\Repository\TravelRepository;
use App\Domain\User\Repository\UserRepository;
use App\Domain\User\ValueObject\UserId;
use App\Domain\Weather\Service\WeatherProvider;
use App\Infrastructure\Weather\Repository\DoctrineWeatherForecastRepository;

class AddJournalEntryService implements UsesCasesService
{
    private TravelRepository $travelRepository;
    private UserRepository $userRepository;
    private JournalEntryRepository $journalRepository;
    private ?WeatherProvider $weatherProvider;
    private ?DoctrineWeatherForecastRepository $forecastRepo;

    public function __construct(
        TravelRepository $travelRepository,
        UserRepository $userRepository,
        JournalEntryRepository $journalRepository,
        ?WeatherProvider $weatherProvider = null,
        ?DoctrineWeatherForecastRepository $forecastRepo = null
    ) {
        $this->travelRepository = $travelRepository;
        $this->userRepository = $userRepository;
        $this->journalRepository = $journalRepository;
        $this->weatherProvider = $weatherProvider;
        $this->forecastRepo = $forecastRepo;
    }

    public function __invoke(AddJournalEntryCommand $command): JournalEntry
    {
        $travel = $this->travelRepository->ofIdOrFail($command->getTravelId());
        $author = $this->userRepository->ofIdOrFail(new UserId($command->getAuthorId()));

        if (!$this->canWrite($travel, $author)) {
            throw new \RuntimeException('Not allowed to write journal for this travel.');
        }

        $entryDate = \DateTime::createFromFormat('Y-m-d', $command->getEntryDate());
        if (!$entryDate) {
            throw new \InvalidArgumentException('Invalid entry date format. Expected Y-m-d.');
        }

        $entry = new JournalEntry($travel, $author, $entryDate, $command->getContent());
        $entry->setTitle($command->getTitle());
        if ($command->getMood()) {
            $entry->setMood($command->getMood());
        }

        // Auto-cache weather for past dates
        if ($entryDate < new \DateTime('today') && $this->weatherProvider && $this->forecastRepo) {
            $lat = $travel->getLatitude();
            $lng = $travel->getLongitude();
            if ($lat != 0.0 || $lng != 0.0) {
                try {
                    $forecast = $this->forecastRepo->findOrFetch($lat, $lng, $entryDate, $this->weatherProvider);
                    $entry->setWeatherSnapshot(json_encode($forecast->toArray()));
                } catch (\Throwable $e) {
                    // Weather is best-effort, never block entry creation
                }
            }
        }

        $this->journalRepository->save($entry);

        return $entry;
    }

    private function canWrite(\App\Domain\Travel\Model\Travel $travel, $user): bool
    {
        if ($travel->getUser()->getId()->id() === $user->getId()->id()) {
            return true;
        }
        foreach ($travel->getSharedusers() as $shared) {
            if ($shared->getId()->id() === $user->getId()->id()) {
                return true;
            }
        }

        return false;
    }
}
