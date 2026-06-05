<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AssetLookupService
{
    public function search(string $isin): array
    {
        $token = config('services.eodhd.token');

        if (!$token) {
            return ['status' => 'missing-token', 'results' => []];
        }

        $response = Http::get("https://eodhd.com/api/search/{$isin}", [
            'api_token' => $token,
            'fmt' => 'json',
        ]);

        if (!$response->successful()) {
            return ['status' => 'error', 'results' => []];
        }

        $results = collect($response->json() ?: [])
            ->filter(fn($result) => ($result['ISIN'] ?? null) === $isin)
            ->map(fn($result) => [
                'ticker' => $result['Code'] ?? null,
                'country' => $this->mapCountry($result['Country'] ?? null),
                'name' => $result['Name'] ?? null,
                'type' => $this->mapType($result['Type'] ?? null),
                'exchange' => $result['Exchange'] ?? null,
                'isPrimary' => (bool) ($result['isPrimary'] ?? false),
            ])
            ->values()
            ->all();

        return ['status' => $results ? 'ok' : 'not-found', 'results' => $results];
    }

    private function mapType($value): string
    {
        $value = strtolower((string) $value);

        return match (true) {
            str_contains($value, 'bank') => 'Bank',
            str_contains($value, 'etf'), str_contains($value, 'fund'), str_contains($value, 'mutual') => 'Fund',
            str_contains($value, 'stock'), str_contains($value, 'common') => 'Stock',
            default => 'Other',
        };
    }

    private function mapCountry($value): string
    {
        return match (strtolower((string) $value)) {
            'norway', 'nor', 'no' => 'NO',
            'sweden', 'swe', 'se' => 'SE',
            'denmark', 'dnk', 'dk' => 'DK',
            'germany', 'deu', 'de' => 'DE',
            'france', 'fra', 'f' => 'F',
            'spain', 'esp', 'es' => 'ES',
            'united states', 'usa', 'us' => 'US',
            'united kingdom', 'gbr', 'uk' => 'UK',
            default => 'Other',
        };
    }
}
