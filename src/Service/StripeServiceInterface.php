<?php

declare(strict_types=1);

namespace App\Service;

interface StripeServiceInterface
{
    public function createSession(int $amount): string;
}