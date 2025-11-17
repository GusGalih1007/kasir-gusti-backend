<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Exception;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$clientKey = config('services.midtrans.client_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = config('services.midtrans.is_sanitized');
        Config::$is3ds = config('services.midtrans.is_3ds');
    }

    /**
     * Create Snap transaction and return token
     */
    public function createSnapTransaction(array $transactionData)
    {
        try {
            $snapToken = Snap::getSnapToken($transactionData);
            return $snapToken;
        } catch (Exception $e) {
            throw new Exception('Midtrans Error: ' . $e->getMessage());
        }
    }

    /**
     * Get Snap redirect URL with token
     */
    public function getSnapRedirectUrl($snapToken)
    {
        // Midtrans provides a standard URL format for Snap
        $baseUrl = config('services.midtrans.is_production') 
            ? 'https://app.midtrans.com/snap/v2/vtweb/'
            : 'https://app.sandbox.midtrans.com/snap/v2/vtweb/';
            
        return $baseUrl . $snapToken;
    }
    
    /**
     * Check if token is expired
     */
    public function isTokenExpired($expiresAt)
    {
        if (!$expiresAt) return true;
        
        return now()->greaterThan($expiresAt);
    }

    /**
     * Regenerate Snap token for existing transaction
     */
    public function regenerateSnapToken(array $transactionData)
    {
        return $this->createSnapTransaction($transactionData);
    }
}