<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsDeliveryService
{
    public function send(string $to, string $message): void
    {
        $to = trim($to);
        if ($to === '') {
            return;
        }

        $driver = config('services.sms.driver', 'sendazi');

        if ($driver === 'sendazi' && blank(config('services.sms.api_key'))) {
            $driver = 'log';
        }

        match ($driver) {
            'sendazi' => $this->sendViaSendazi($to, $message),
            'http' => $this->sendViaHttp($to, $message),
            default => $this->sendViaLog($to, $message),
        };
    }

    private function sendViaSendazi(string $to, string $message): void
    {
        $campaignName = config('services.sms.campaign_name');
        $result = app(SendaziService::class)->send(
            $to,
            $message,
            is_string($campaignName) ? $campaignName : null,
        );

        if (! ($result['ok'] ?? false)) {
            Log::warning('SMS not delivered via Sendazi', [
                'to' => $to,
                'error' => $result['error'] ?? null,
                'status' => $result['status'] ?? null,
            ]);
        }
    }

    private function sendViaLog(string $to, string $message): void
    {
        Log::info('SMS notification (log driver)', [
            'to' => $to,
            'message' => $message,
        ]);
    }

    private function sendViaHttp(string $to, string $message): void
    {
        $url = config('services.sms.http_url');
        if (blank($url)) {
            Log::warning('SMS HTTP driver selected but services.sms.http_url is empty; using log driver.', [
                'to' => $to,
            ]);
            $this->sendViaLog($to, $message);

            return;
        }

        $payload = [
            'to' => $to,
            'message' => $message,
        ];

        $token = config('services.sms.http_token');
        $headers = filled($token) ? ['Authorization' => 'Bearer '.$token] : [];

        $response = Http::withHeaders($headers)
            ->timeout(15)
            ->acceptJson()
            ->post($url, $payload);

        if ($response->failed()) {
            Log::warning('SMS HTTP delivery failed', [
                'to' => $to,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        }
    }
}
