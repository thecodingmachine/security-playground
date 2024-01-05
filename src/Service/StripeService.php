<?php

declare(strict_types=1);

namespace App\Service;

use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Stripe\StripeClient;
use Symfony\Component\Routing\RouterInterface;

final class StripeService implements StripeServiceInterface
{
    public function __construct(private readonly RouterInterface $router, private readonly string $secretKey)
    {
    }

    public function createSession(int $amount): Session
    {
        Stripe::setApiKey($this->secretKey);

        return Session::create([
            'line_items' => [
                [
                    'quantity' => 1,
                    'price_data' => [
                        'currency' => 'EUR',
                        'product_data' => [
                            'name' => "VIP"
                        ],
                        'unit_amount'  => $amount * 100
                    ]
                ]
            ],
            'mode' => 'payment',
            'success_url' => $this->router->generate('app_account_stripe_payment_success', [], RouterInterface::ABSOLUTE_URL),
            'cancel_url' => $this->router->generate('app_account_stripe_payment_cancel', [], RouterInterface::ABSOLUTE_URL),
        ]);
    }

    /**
     * @throws ApiErrorException
     */
    public function findSessionById(string $id): Session
    {
        $stripe = new StripeClient($this->secretKey);

        return $stripe->checkout->sessions->retrieve($id);
    }
}