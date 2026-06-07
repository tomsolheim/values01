<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Asset;
use App\Models\Bundle;
use App\Models\Transaction as TransactionModel;
use App\Models\Variable;
use App\Services\TransactionImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class TransactionImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_transactions_table_exists_with_expected_columns(): void
    {
        $columns = Schema::getColumnListing('transactions');

        foreach ([
            'source_id', 'booked_date', 'trade_date', 'settlement_date', 'portfolio', 'transaction_type',
            'security_name', 'isin', 'quantity', 'price', 'interest', 'total_fees', 'fees_currency',
            'amount', 'amount_currency', 'purchase_value', 'purchase_value_currency', 'result',
            'result_currency', 'total_quantity', 'balance', 'exchange_rate', 'transaction_text',
            'cancellation_date', 'contract_note_number', 'verification_number', 'brokerage',
            'brokerage_currency', 'currency_rate', 'initial_interest', 'created_at', 'updated_at',
        ] as $column) {
            $this->assertContains($column, $columns);
        }
    }

    public function test_import_accepts_sample_header_and_imports_all_rows(): void
    {
        $result = app(TransactionImportService::class)->import($this->sampleFile());

        $this->assertSame(3, $result['imported']);
        $this->assertDatabaseHas('transactions', ['source_id' => '1', 'isin' => 'NO000000001']);
        $this->assertDatabaseHas('transactions', ['source_id' => '2', 'isin' => 'SE000000002']);
        $this->assertDatabaseHas('transactions', ['source_id' => '3', 'isin' => null]);
    }

    public function test_import_can_filter_to_one_isin(): void
    {
        $result = app(TransactionImportService::class)->import($this->sampleFile(), 'NO000000001');

        $this->assertSame(1, $result['imported']);
        $this->assertSame(2, $result['skipped']);
        $this->assertDatabaseHas('transactions', ['source_id' => '1']);
        $this->assertDatabaseMissing('transactions', ['source_id' => '2']);
        $this->assertDatabaseMissing('transactions', ['source_id' => '3']);
    }

    public function test_import_maps_repeated_valuta_columns_to_distinct_fields(): void
    {
        app(TransactionImportService::class)->import($this->sampleFile());

        $transaction = TransactionModel::where('source_id', '1')->firstOrFail();

        $this->assertSame('NOK', $transaction->fees_currency);
        $this->assertSame('USD', $transaction->amount_currency);
        $this->assertSame('EUR', $transaction->purchase_value_currency);
        $this->assertSame('SEK', $transaction->result_currency);
        $this->assertSame('DKK', $transaction->brokerage_currency);
    }

    public function test_import_normalizes_comma_decimal_numbers(): void
    {
        app(TransactionImportService::class)->import($this->sampleFile());

        $transaction = TransactionModel::where('source_id', '1')->firstOrFail();

        $this->assertSame(10.5, (float) $transaction->quantity);
        $this->assertSame(1234.56, (float) $transaction->amount);
    }

    public function test_duplicate_source_id_values_are_not_imported_twice(): void
    {
        $importer = app(TransactionImportService::class);

        $importer->import($this->sampleFile());
        $result = $importer->import($this->sampleFile());

        $this->assertSame(0, $result['imported']);
        $this->assertSame(3, TransactionModel::count());
    }

    public function test_add_assets_creates_new_stock_assets_with_import_bundle_and_unknown_area(): void
    {
        $result = app(TransactionImportService::class)->import($this->sampleFile(), null, true);

        $this->assertSame(2, $result['assetsCreated']);
        $bundle = Bundle::where('name', 'Import')->firstOrFail();
        $area = Area::where('name', 'Unknown')->firstOrFail();

        $this->assertDatabaseHas('assets', [
            'type' => 'Stock',
            'isin' => 'NO000000001',
            'name' => 'Company One',
            'bundle_id' => $bundle->id,
            'area_id' => $area->id,
        ]);
    }

    public function test_add_assets_does_not_duplicate_existing_asset_names(): void
    {
        Asset::create(['type' => 'Stock', 'isin' => 'NO000000001', 'name' => 'Company One']);

        app(TransactionImportService::class)->import($this->sampleFile(), null, true);

        $this->assertSame(1, Asset::where('name', 'Company One')->count());
    }

    public function test_repeated_import_with_add_assets_can_create_missing_assets_from_duplicate_transactions(): void
    {
        $importer = app(TransactionImportService::class);

        $importer->import($this->sampleFile());
        $result = $importer->import($this->sampleFile(), null, true);

        $this->assertSame(0, $result['imported']);
        $this->assertSame(3, $result['skipped']);
        $this->assertSame(2, $result['assetsCreated']);
        $this->assertDatabaseHas('assets', ['name' => 'Company One']);
        $this->assertDatabaseHas('assets', ['name' => 'Company Two']);
    }

    public function test_add_assets_respects_one_isin_filter(): void
    {
        $result = app(TransactionImportService::class)->import($this->sampleFile(), 'NO000000001', true);

        $this->assertSame(1, $result['assetsCreated']);
        $this->assertDatabaseHas('assets', ['name' => 'Company One']);
        $this->assertDatabaseMissing('assets', ['name' => 'Company Two']);
    }

    public function test_add_assets_does_not_create_assets_without_isin(): void
    {
        $result = app(TransactionImportService::class)->import($this->sampleFile([
            ['4', '04.06.2026', '', '', 'P1', 'Utbytte', 'Named Cash Movement', '', '', '', '', '', '', '12,50', 'NOK', '', '', '', '', '', '', '', 'Dividend', '', '', '', '', '', '', ''],
        ]), null, true);

        $this->assertSame(2, $result['assetsCreated']);
        $this->assertDatabaseMissing('assets', ['name' => 'Named Cash Movement']);
    }

    public function test_import_widget_shows_current_transaction_count(): void
    {
        TransactionModel::create(['source_id' => 'count-1']);
        TransactionModel::create(['source_id' => 'count-2']);

        Livewire::test('transaction-import-widget')
            ->assertSet('transactionCount', 2)
            ->assertSee('Transactions: 2');
    }

    public function test_import_widget_refresh_updates_transaction_count(): void
    {
        $component = Livewire::test('transaction-import-widget')
            ->assertSet('transactionCount', 0);

        TransactionModel::create(['source_id' => 'refresh-1']);

        $component
            ->call('refreshTransactionCount')
            ->assertSet('transactionCount', 1)
            ->assertSee('Transactions: 1');
    }

    public function test_import_widget_delete_all_requires_confirmation_markup(): void
    {
        Livewire::test('transaction-import-widget')
            ->assertSee('Delete all transactions')
            ->assertSee('wire:confirm="All transaction records will be permanently deleted. This cannot be undone. Continue?"', false);
    }

    public function test_import_widget_delete_all_removes_only_transactions_and_updates_count(): void
    {
        TransactionModel::create(['source_id' => 'delete-1']);
        TransactionModel::create(['source_id' => 'delete-2']);
        $bundle = Bundle::create(['name' => 'Keep Bundle']);
        $area = Area::create(['name' => 'Keep Area']);
        Asset::create(['type' => 'Stock', 'name' => 'Keep Asset', 'bundle_id' => $bundle->id, 'area_id' => $area->id]);
        Variable::create(['name' => 'keep_variable', 'value' => '1', 'group' => 'test', 'comment' => 'Keep variable.']);

        Livewire::test('transaction-import-widget')
            ->assertSet('transactionCount', 2)
            ->call('deleteAllTransactions')
            ->assertSet('transactionCount', 0)
            ->assertSee('Transactions: 0')
            ->assertSee('All transaction records were permanently deleted.');

        $this->assertSame(0, TransactionModel::count());
        $this->assertDatabaseHas('bundles', ['name' => 'Keep Bundle']);
        $this->assertDatabaseHas('areas', ['name' => 'Keep Area']);
        $this->assertDatabaseHas('assets', ['name' => 'Keep Asset']);
        $this->assertDatabaseHas('variables', ['name' => 'keep_variable']);
    }

    private function sampleFile(array $extraRows = []): string
    {
        $headers = [
            'Id', 'Bokføringsdag', 'Handelsdag', 'Oppgjørsdag', 'Portefølje', 'Transaksjonstype',
            'Verdipapir', 'ISIN', 'Antall', 'Kurs', 'Rente', 'Totale Avgifter', 'Valuta', 'Beløp',
            'Valuta', 'Kjøpsverdi', 'Valuta', 'Resultat', 'Valuta', 'Totalt antall', 'Saldo',
            'Vekslingskurs', 'Transaksjonstekst', 'Makuleringsdato', 'Sluttseddelnummer',
            'Verifikationsnummer', 'Kurtasje', 'Valuta', 'Valutakurs', 'Innledende rente',
        ];

        $rows = [
            ['1', '01.06.2026', '01.06.2026', '03.06.2026', 'P1', 'Kjøp', 'Company One', 'NO000000001', '10,5', '100,25', '', '12,34', 'NOK', '1.234,56', 'USD', '1.111,11', 'EUR', '22,22', 'SEK', '10,5', '1000', '11,20', 'Text', '', 'CN1', 'VN1', '1,23', 'DKK', '10,10', ''],
            ['2', '02.06.2026', '02.06.2026', '04.06.2026', 'P1', 'Salg', 'Company Two', 'SE000000002', '2', '50', '', '0', 'NOK', '100', 'NOK', '90', 'NOK', '10', 'NOK', '0', '100', '1', 'Text', '', 'CN2', 'VN2', '0', 'NOK', '1', ''],
            ['3', '03.06.2026', '', '', 'P1', 'Avgift', '', '', '', '', '', '', '', '-25,50', 'NOK', '', '', '', '', '', '', '', 'Fee', '', '', '', '', '', '', ''],
        ];

        $rows = array_merge($rows, $extraRows);

        $contents = implode("\n", array_map(fn($row) => implode("\t", $row), array_merge([$headers], $rows)));
        $path = tempnam(sys_get_temp_dir(), 'values-import-');
        file_put_contents($path, mb_convert_encoding($contents, 'UTF-16LE', 'UTF-8'));

        return $path;
    }
}
