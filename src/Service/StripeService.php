<?php

declare(strict_types=1);

namespace App\Service;

use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Component\Routing\RouterInterface;

final class StripeService implements StripeServiceInterface
{
    public function __construct(private readonly RouterInterface $router, private readonly string $secretKey)
    {
    }

    public function createSession(int $amount): string
    {
        Stripe::setApiKey($this->secretKey);

        $session = Session::create([
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

        return $session->url;
    }
}