<?php

namespace App\Commands\Restaurant;

use App\Commands\CommandInterface;
use App\DTO\Visits\VisitDto;

class SaveVisitCommand implements CommandInterface
{
    public function __construct(
        protected VisitDto $visitDto
    ) {
    }

    public function toArray(): array
    {
        return $this->visitDto->toArray();
    }
}
