<?php

namespace App\Service;

use Stripe\StripeClient;

class StripeService
{

    private $stripe;

    public function __construct()
    {
    }

    public function setApiKey(string $stripeApiKey): void
    {
        $this->stripe = new StripeClient($stripeApiKey);
    }
    
    public function createPaymentIntent(int $amount, string $currency): array
    {
        $paymentIntent = $this->stripe->paymentIntents->create([
            'amount' => $amount,
            'currency' => $currency,
        ]);

        return [
            'client_secret' => $paymentIntent->client_secret,
            'payment_intent_id' => $paymentIntent->id,
        ];
    }
}
