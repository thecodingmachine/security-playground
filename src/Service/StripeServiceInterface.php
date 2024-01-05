<?php

declare(strict_types=1);

namespace App\Service;

use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;

interface StripeServiceInterface
{
    public function createSession(int $amount): Session;

    /**
     * @throws ApiErrorException
     */
    public function findSessionById(string $id): Session;
}
