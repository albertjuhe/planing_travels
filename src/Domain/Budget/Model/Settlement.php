<?php

namespace App\Domain\Budget\Model;

use App\Domain\Travel\Model\Travel;
use App\Domain\User\Model\User;
use Ramsey\Uuid\Uuid;

class Settlement
{
    /** @var string */
    private $id;

    /** @var Travel */
    private $travel;

    /** @var User */
    private $fromUser;

    /** @var User */
    private $toUser;

    /** @var float */
    private $amount;

    /** @var string */
    private $currency;

    /** @var \DateTime */
    private $settledAt;

    /** @var string|null */
    private $note;

    public function __construct(
        Travel $travel,
        User $fromUser,
        User $toUser,
        float $amount,
        string $currency,
        ?string $note = null
    ) {
        $this->id = Uuid::uuid4()->toString();
        $this->travel = $travel;
        $this->fromUser = $fromUser;
        $this->toUser = $toUser;
        $this->amount = $amount;
        $this->currency = strtoupper($currency);
        $this->note = $note;
        $this->settledAt = new \DateTime();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTravel(): Travel
    {
        return $this->travel;
    }

    public function getFromUser(): User
    {
        return $this->fromUser;
    }

    public function getToUser(): User
    {
        return $this->toUser;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getSettledAt(): \DateTime
    {
        return $this->settledAt;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'fromUserId' => $this->fromUser->getId()->id(),
            'fromUsername' => $this->fromUser->getUsername(),
            'toUserId' => $this->toUser->getId()->id(),
            'toUsername' => $this->toUser->getUsername(),
            'amount' => $this->amount,
            'currency' => $this->currency,
            'settledAt' => $this->settledAt->format('Y-m-d H:i'),
            'note' => $this->note,
        ];
    }
}
