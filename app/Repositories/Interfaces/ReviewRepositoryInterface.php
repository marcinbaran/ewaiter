<?php

namespace App\Repositories\Interfaces;

use App\Models\Review;

interface ReviewRepositoryInterface
{
    public function createReview(array $addressData): Review;
}
