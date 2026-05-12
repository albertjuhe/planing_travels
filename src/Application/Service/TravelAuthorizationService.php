<?php

namespace App\Application\Service;

use App\Domain\Travel\Model\Travel;
use App\Domain\User\Model\User;

class TravelAuthorizationService
{
    public function canAccess(Travel $travel, User $user): bool
    {
        if ($travel->isPublished()) {
            return true;
        }

        return $this->canEdit($travel, $user);
    }

    public function canEdit(Travel $travel, User $user): bool
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

    public function canClone(Travel $travel, User $user): bool
    {
        return $this->canAccess($travel, $user);
    }
}
