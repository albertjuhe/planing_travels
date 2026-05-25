<?php

namespace App\Application\Command\Travel;

use App\Application\Command\Command;

final class GenerateItineraryCommand implements Command
{
    public function __construct(
        public readonly string $travelId,
        public readonly string $userId,
        public readonly string $mode,
        public readonly string $locale,
        public readonly string $additionalNotes = '',
    ) {
    }
}
