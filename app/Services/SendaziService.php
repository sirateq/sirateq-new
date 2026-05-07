<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendaziService
{
    private const BASE_URL = 'https://sendazi.com/api/v1';

    /**
     * @return array{ok: bool, response?: mixed, status?: int, error?: string}
     */
    public function send(string $phone, string $message, ?string $campaignName = null): array
    {
        $settings = config('services.sms');
        $apiKey = $settings['api_key'] ?? null;
        $senderId = $settings['sender_id'] ?? null;

        if (blank($apiKey)) {
            return ['ok' => false, 'error' => 'missing_api_key'];
        }

        $response = Http::withToken($apiKey)
            ->timeout(20)
            ->acceptJson()
            ->get(self::BASE_URL.'/sms-campaigns/quick', [
                'to' => $phone,
                'message' => $message,
                'sender_id' => $senderId,
                'campaign_name' => $campaignName ?? config('services.sms.campaign_name', 'Api Quick Campaign'),
                'key' => $apiKey,
            ]);

        if ($response->failed()) {
            Log::warning('Sendazi SMS request failed', [
                'to' => $phone,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return ['ok' => false, 'status' => $response->status()];
        }

        return ['ok' => true, 'response' => $response->json()];
    }
}
