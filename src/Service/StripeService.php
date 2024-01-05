<?php

declare(strict_types=1);

namespace App\Service;

use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\StripeClient;

final class StripeService implements StripeServiceInterface
{
    public function __construct(private readonly string $secretKey, private readonly string $domain)
    {
    }

    public function createSession(int $amount): string
    {
        Stripe::setApiKey($this->secretKey);

        $checkoutSession = Session::create([
            'line_items' => [[
                'price' => $this->createProduct($amount),
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => "http://$this->domain/stripe/success",
            'cancel_url' => "http://$this->domain/stripe/cancel",
        ]);

        return $checkoutSession->url;
    }

    private function createProduct(int $amount): string
    {
        $stripe = new StripeClient($this->secretKey);

        $product = $stripe->products->create([
            'name' => 'VIP',
            'description' => 'Devenir membre VIP',
        ]);

        $price = $stripe->prices->create([
            'unit_amount' => $amount * 100,
            'currency' => 'eur',
            'product' => $product['id'],
        ]);

        return $price->id;
    }
}