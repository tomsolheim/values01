<?php

namespace App\Services;

use App\Models\Area;
use App\Models\Asset;
use App\Models\Bundle;
use App\Models\Transaction;

class TransactionImportService
{
    public function import(string $path, ?string $isinFilter = null, bool $addAssets = false): array
    {
        $rows = $this->rows($path);
        $imported = 0;
        $skipped = 0;
        $assetsCreated = 0;

        foreach ($rows as $row) {
            $payload = $this->payload($row);

            if (!$payload['source_id']) {
                $skipped++;
                continue;
            }

            if ($isinFilter && ($payload['isin'] ?? '') !== $isinFilter) {
                $skipped++;
                continue;
            }

            if ($addAssets && $this->createAssetFromRow($row)) {
                $assetsCreated++;
            }

            if (Transaction::query()->where('source_id', $payload['source_id'])->exists()) {
                $skipped++;
                continue;
            }

            Transaction::create($payload);
            $imported++;
        }

        return compact('imported', 'skipped', 'assetsCreated');
    }

    public function rows(string $path): array
    {
        $contents = file_get_contents($path);
        $contents = $this->toUtf8($contents ?: '');
        $lines = preg_split('/\R/u', trim($contents)) ?: [];

        if (count($lines) < 2) {
            return [];
        }

        $headers = $this->headers(str_getcsv(array_shift($lines), "\t"));
        $rows = [];

        foreach ($lines as $line) {
            if (trim($line) === '') {
                continue;
            }

            $values = str_getcsv($line, "\t");
            $rows[] = array_combine($headers, array_pad($values, count($headers), null));
        }

        return array_filter($rows);
    }

    private function headers(array $headers): array
    {
        $valutaCount = 0;

        return array_map(function ($header) use (&$valutaCount) {
            $header = trim((string) $header, " \t\n\r\0\x0B\xEF\xBB\xBF");

            if ($header !== 'Valuta') {
                return $header;
            }

            return match (++$valutaCount) {
                1 => 'Valuta fees',
                2 => 'Valuta amount',
                3 => 'Valuta purchase_value',
                4 => 'Valuta result',
                5 => 'Valuta brokerage',
                default => 'Valuta '.$valutaCount,
            };
        }, $headers);
    }

    private function payload(array $row): array
    {
        return [
            'source_id' => $this->blankToNull($row['Id'] ?? null),
            'booked_date' => $this->date($row['Bokføringsdag'] ?? null),
            'trade_date' => $this->date($row['Handelsdag'] ?? null),
            'settlement_date' => $this->date($row['Oppgjørsdag'] ?? null),
            'portfolio' => $this->blankToNull($row['Portefølje'] ?? null),
            'transaction_type' => $this->blankToNull($row['Transaksjonstype'] ?? null),
            'security_name' => $this->blankToNull($row['Verdipapir'] ?? null),
            'isin' => $this->blankToNull($row['ISIN'] ?? null),
            'quantity' => $this->decimal($row['Antall'] ?? null),
            'price' => $this->decimal($row['Kurs'] ?? null),
            'interest' => $this->decimal($row['Rente'] ?? null),
            'total_fees' => $this->decimal($row['Totale Avgifter'] ?? null),
            'fees_currency' => $this->blankToNull($row['Valuta fees'] ?? null),
            'amount' => $this->decimal($row['Beløp'] ?? null),
            'amount_currency' => $this->blankToNull($row['Valuta amount'] ?? null),
            'purchase_value' => $this->decimal($row['Kjøpsverdi'] ?? null),
            'purchase_value_currency' => $this->blankToNull($row['Valuta purchase_value'] ?? null),
            'result' => $this->decimal($row['Resultat'] ?? null),
            'result_currency' => $this->blankToNull($row['Valuta result'] ?? null),
            'total_quantity' => $this->decimal($row['Totalt antall'] ?? null),
            'balance' => $this->decimal($row['Saldo'] ?? null),
            'exchange_rate' => $this->decimal($row['Vekslingskurs'] ?? null),
            'transaction_text' => $this->blankToNull($row['Transaksjonstekst'] ?? null),
            'cancellation_date' => $this->date($row['Makuleringsdato'] ?? null),
            'contract_note_number' => $this->blankToNull($row['Sluttseddelnummer'] ?? null),
            'verification_number' => $this->blankToNull($row['Verifikationsnummer'] ?? null),
            'brokerage' => $this->decimal($row['Kurtasje'] ?? null),
            'brokerage_currency' => $this->blankToNull($row['Valuta brokerage'] ?? null),
            'currency_rate' => $this->decimal($row['Valutakurs'] ?? null),
            'initial_interest' => $this->decimal($row['Innledende rente'] ?? null),
        ];
    }

    private function createAssetFromRow(array $row): bool
    {
        $name = $this->blankToNull($row['Verdipapir'] ?? null);
        $isin = $this->blankToNull($row['ISIN'] ?? null);

        if (!$name || !$isin || Asset::query()->where('name', $name)->exists()) {
            return false;
        }

        $bundle = Bundle::firstOrCreate(['name' => 'Import'], ['comment' => 'Created by transaction import.']);
        $area = Area::firstOrCreate(['name' => 'Unknown'], ['comment' => 'Created by transaction import.']);

        Asset::create([
            'type' => 'Stock',
            'isin' => $isin,
            'name' => $name,
            'bundle_id' => $bundle->id,
            'area_id' => $area->id,
        ]);

        return true;
    }

    private function toUtf8(string $contents): string
    {
        if (str_starts_with($contents, "\xFF\xFE") || str_starts_with($contents, "\xFE\xFF")) {
            return mb_convert_encoding($contents, 'UTF-8', 'UTF-16');
        }

        if (str_contains($contents, "\0")) {
            return mb_convert_encoding($contents, 'UTF-8', 'UTF-16LE');
        }

        return $contents;
    }

    private function decimal($value): ?string
    {
        $value = $this->blankToNull($value);

        if ($value === null) {
            return null;
        }

        $value = str_replace(["\xc2\xa0", ' '], '', $value);

        if (str_contains($value, ',')) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        }

        return is_numeric($value) ? $value : null;
    }

    private function date($value): ?string
    {
        $value = $this->blankToNull($value);

        if ($value === null) {
            return null;
        }

        foreach (['d.m.Y', 'Y-m-d'] as $format) {
            $date = \DateTime::createFromFormat($format, $value);

            if ($date) {
                return $date->format('Y-m-d');
            }
        }

        return null;
    }

    private function blankToNull($value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
