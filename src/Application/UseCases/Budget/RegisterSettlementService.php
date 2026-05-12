<?php

namespace App\Application\UseCases\Budget;

use App\Application\Command\Budget\RegisterSettlementCommand;
use App\Application\UseCases\UsesCasesService;
use App\Application\Service\TravelAuthorizationService;
use App\Domain\Budget\Model\Settlement;
use App\Domain\User\ValueObject\UserId;
use App\Infrastructure\TravelBundle\Repository\DoctrineTravelRepository;
use App\Infrastructure\UserBundle\Repository\DoctrineUserRepository;
use App\Infrastructure\WebSocket\WebSocketNotifier;
use Doctrine\ORM\EntityManagerInterface;

class RegisterSettlementService implements UsesCasesService
{
    private DoctrineTravelRepository $travelRepo;
    private DoctrineUserRepository $userRepo;
    private EntityManagerInterface $em;
    private TravelAuthorizationService $authService;
    private WebSocketNotifier $notifier;

    public function __construct(
        DoctrineTravelRepository $travelRepo,
        DoctrineUserRepository $userRepo,
        EntityManagerInterface $em,
        TravelAuthorizationService $authService,
        WebSocketNotifier $notifier
    ) {
        $this->travelRepo = $travelRepo;
        $this->userRepo = $userRepo;
        $this->em = $em;
        $this->authService = $authService;
        $this->notifier = $notifier;
    }

    public function __invoke(RegisterSettlementCommand $command): Settlement
    {
        $travel = $this->travelRepo->ofIdOrFail($command->getTravelId());
        $fromUser = $this->userRepo->ofIdOrFail(new UserId($command->getFromUserId()));
        $toUser = $this->userRepo->ofIdOrFail(new UserId($command->getToUserId()));

        if (!$this->authService->canEdit($travel, $fromUser)) {
            throw new \RuntimeException('Not allowed to register a settlement for this travel.');
        }

        $settlement = new Settlement(
            $travel,
            $fromUser,
            $toUser,
            $command->getAmount(),
            $command->getCurrency(),
            $command->getNote()
        );

        $this->em->persist($settlement);
        $this->em->flush();

        $this->notifier->broadcast($travel->getId()->id(), [
            'type' => 'settlement.registered',
            'travelId' => $travel->getId()->id(),
            'fromUsername' => $fromUser->getUsername(),
            'toUsername' => $toUser->getUsername(),
            'amount' => $command->getAmount(),
            'currency' => $command->getCurrency(),
        ]);

        return $settlement;
    }
}
