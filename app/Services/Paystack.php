<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Paystack
{
    /**
     * Verify a transaction by its reference.
     *
     * @return array{status: bool, success: bool, data: array<string, mixed>, message: string}
     */
    public function verify(string $reference): array
    {
        if (! $this->isConfigured()) {
            return [
                'status' => false,
                'success' => false,
                'data' => [],
                'message' => 'Paystack secret key is not configured.',
            ];
        }

        try {
            $response = Http::withToken((string) config('services.paystack.secret_key'))
                ->acceptJson()
                ->timeout(15)
                ->retry(2, 250)
                ->get(rtrim((string) config('services.paystack.base_url'), '/').'/transaction/verify/'.urlencode($reference));
        } catch (ConnectionException $exception) {
            Log::warning('Paystack verify connection error', [
                'reference' => $reference,
                'error' => $exception->getMessage(),
            ]);

            return [
                'status' => false,
                'success' => false,
                'data' => [],
                'message' => 'Unable to reach Paystack. Please try again.',
            ];
        }

        $payload = $response->json() ?? [];
        $data = (array) ($payload['data'] ?? []);
        $message = (string) ($payload['message'] ?? '');

        $apiOk = $response->successful() && (bool) ($payload['status'] ?? false);
        $charged = ($data['status'] ?? null) === 'success';

        return [
            'status' => $apiOk,
            'success' => $apiOk && $charged,
            'data' => $data,
            'message' => $message,
        ];
    }

    public function publicKey(): ?string
    {
        $key = config('services.paystack.public_key');

        return is_string($key) && $key !== '' ? $key : null;
    }

    public function currency(): string
    {
        return (string) config('services.paystack.currency', 'GHS');
    }

    public function isConfigured(): bool
    {
        return filled(config('services.paystack.secret_key')) && filled(config('services.paystack.public_key'));
    }

    public function generateReference(string $prefix = 'SQ'): string
    {
        return strtoupper($prefix).'-'.now()->format('YmdHis').'-'.strtoupper((string) str()->random(8));
    }
}
