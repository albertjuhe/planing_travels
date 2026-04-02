<?php

namespace App\Domain\User\Model;

class PasswordResetToken
{
    /**
     * @var int|null
     */
    private $id;

    /**
     * @var User
     */
    private $user;

    /**
     * @var string
     */
    private $tokenHash;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $expiresAt;

    /**
     * @var \DateTime|null
     */
    private $usedAt;

    public function __construct(User $user, string $tokenHash, \DateTime $expiresAt)
    {
        $this->user = $user;
        $this->tokenHash = $tokenHash;
        $this->expiresAt = $expiresAt;
        $this->createdAt = new \DateTime();
        $this->usedAt = null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getTokenHash(): string
    {
        return $this->tokenHash;
    }

    public function getExpiresAt(): \DateTime
    {
        return $this->expiresAt;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt < new \DateTime();
    }

    public function isUsed(): bool
    {
        return null !== $this->usedAt;
    }

    public function canBeUsed(): bool
    {
        return !$this->isUsed() && !$this->isExpired();
    }

    public function markAsUsed(): void
    {
        $this->usedAt = new \DateTime();
    }
}
