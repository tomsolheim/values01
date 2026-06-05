<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Asset;
use App\Models\Bundle;
use App\Models\Variable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class AssetWidgetTest extends TestCase
{
    use RefreshDatabase;

    private function setLookupCounter(string $value = '5'): void
    {
        Variable::updateOrCreate(
            ['name' => 'isin_counter'],
            ['value' => $value, 'group' => 'system', 'comment' => 'ISIN lookup quota.']
        );
    }

    public function test_assets_table_exists_with_expected_columns(): void
    {
        $columns = Schema::getColumnListing('assets');

        foreach (['id', 'type', 'isin', 'ticker', 'country', 'name', 'bundle_id', 'area_id', 'comment', 'created_at', 'updated_at'] as $column) {
            $this->assertContains($column, $columns);
        }
    }

    public function test_can_create_asset_with_required_fields(): void
    {
        Asset::create([
            'type' => 'Stock',
            'name' => 'Test Asset',
        ]);

        $this->assertDatabaseHas('assets', [
            'type' => 'Stock',
            'name' => 'Test Asset',
        ]);
    }

    public function test_can_create_asset_with_optional_fields_and_relationships(): void
    {
        $bundle = Bundle::create(['name' => 'Growth']);
        $area = Area::create(['name' => 'Equities']);

        Asset::create([
            'type' => 'Fund',
            'isin' => 'NO0012345678',
            'ticker' => 'ABC',
            'country' => 'NO',
            'name' => 'Full Asset',
            'bundle_id' => $bundle->id,
            'area_id' => $area->id,
            'comment' => 'A test comment',
        ]);

        $this->assertDatabaseHas('assets', [
            'isin' => 'NO0012345678',
            'bundle_id' => $bundle->id,
            'area_id' => $area->id,
        ]);
    }

    public function test_can_update_asset(): void
    {
        $asset = Asset::create(['type' => 'Stock', 'name' => 'Original Name']);

        $asset->update(['name' => 'Updated Name']);

        $this->assertDatabaseHas('assets', ['name' => 'Updated Name']);
        $this->assertDatabaseMissing('assets', ['name' => 'Original Name']);
    }

    public function test_can_delete_asset(): void
    {
        $asset = Asset::create(['type' => 'Stock', 'name' => 'Delete Me']);

        $asset->delete();

        $this->assertDatabaseMissing('assets', ['name' => 'Delete Me']);
    }

    public function test_front_page_shows_asset_widget(): void
    {
        $response = $this->get('/');

        $response->assertSee('Assets');
        $response->assertSee('Show form');
        $response->assertSee('Search assets');
    }

    public function test_asset_list_shows_expected_columns(): void
    {
        $response = $this->get('/');

        foreach (['Type', 'ISIN', 'Tic', 'Country', 'Name', 'Bundle', 'Area', 'Comment'] as $label) {
            $response->assertSee($label);
        }
    }

    public function test_asset_list_displays_bundle_and_area_names(): void
    {
        $bundle = Bundle::create(['name' => 'Growth']);
        $area = Area::create(['name' => 'Equities']);

        Asset::create([
            'type' => 'Stock',
            'name' => 'Listed Asset',
            'bundle_id' => $bundle->id,
            'area_id' => $area->id,
        ]);

        $response = $this->get('/');

        $response->assertSee('Listed Asset');
        $response->assertSee('Growth');
        $response->assertSee('Equities');
    }

    public function test_asset_dropdown_sources_show_existing_bundle_and_area_names(): void
    {
        Bundle::create(['name' => 'Income']);
        Area::create(['name' => 'Bonds']);

        $response = $this->get('/');

        $response->assertSee('Income');
        $response->assertSee('Bonds');
    }

    public function test_asset_list_shows_edit_and_delete_icons(): void
    {
        Asset::create(['type' => 'Stock', 'name' => 'Test Asset']);

        $response = $this->get('/');

        $response->assertSee('bi-pen');
        $response->assertSee('bi-trash');
        $response->assertSee('Edit');
        $response->assertSee('Delete');
    }

    public function test_asset_list_is_paginated(): void
    {
        for ($i = 0; $i < 15; $i++) {
            Asset::create(['type' => 'Stock', 'name' => "Asset {$i}"]);
        }

        $response = $this->get('/');

        $response->assertSee('pagination-sm');
        $response->assertSee('page-item');
        $response->assertSee('page-link');
    }

    public function test_asset_widget_has_csv_import_and_export_controls(): void
    {
        $response = $this->get('/');

        $response->assertSee('asset-csv-import');
        $response->assertSee('Import CSV');
        $response->assertSee('Export CSV');
    }

    public function test_asset_form_shows_lookup_isin_button(): void
    {
        Livewire::test('asset-widget')
            ->set('showForm', true)
            ->assertSee('Lookup ISIN');
    }

    public function test_lookup_isin_shows_configuration_message_without_token(): void
    {
        config(['services.eodhd.token' => null]);
        $this->setLookupCounter();

        Livewire::test('asset-widget')
            ->set('showForm', true)
            ->set('isin', 'NO0012345678')
            ->call('lookupIsin')
            ->assertSet('lookupMessage', 'ISIN lookup needs EODHD_API_TOKEN to be configured.');
    }

    public function test_lookup_isin_requires_valid_isin_counter(): void
    {
        config(['services.eodhd.token' => 'test-token']);
        Http::fake();

        Livewire::test('asset-widget')
            ->set('isin', 'NO0012345678')
            ->call('lookupIsin')
            ->assertSet('lookupMessage', 'ISIN lookup counter is not configured.');

        Http::assertNothingSent();
    }

    public function test_lookup_isin_blocks_at_zero_counter_with_popup_message(): void
    {
        config(['services.eodhd.token' => 'test-token']);
        $this->setLookupCounter('0');
        Http::fake();

        Livewire::test('asset-widget')
            ->set('showForm', true)
            ->set('isin', 'NO0012345678')
            ->call('lookupIsin')
            ->assertSet('lookupPopupMessage', 'todays lookup quota is used')
            ->assertSee('data-popup-message')
            ->assertSee('todays lookup quota is used');

        Http::assertNothingSent();
    }

    public function test_lookup_isin_prefers_primary_result_and_fills_blank_fields_without_saving(): void
    {
        config(['services.eodhd.token' => 'test-token']);
        $this->setLookupCounter('5');
        $area = Area::create(['name' => 'NO']);

        Http::fake([
            'https://eodhd.com/api/search/*' => Http::response([
                ['ISIN' => 'NO0012345678', 'Code' => 'ALT', 'Country' => 'Sweden', 'Name' => 'Alternate', 'Type' => 'ETF', 'isPrimary' => false],
                ['ISIN' => 'NO0012345678', 'Code' => 'MAIN', 'Country' => 'Norway', 'Name' => 'Primary Asset', 'Type' => 'Common Stock', 'isPrimary' => true],
            ]),
        ]);

        Livewire::test('asset-widget')
            ->set('showForm', true)
            ->set('isin', 'NO0012345678')
            ->call('lookupIsin')
            ->assertSet('ticker', 'MAIN')
            ->assertSet('country', 'NO')
            ->assertSet('name', 'Primary Asset')
            ->assertSet('type', 'Stock')
            ->assertSet('area_id', (string) $area->id)
            ->assertSet('lookupMessage', 'Lookup values applied. Save to persist changes.');

        $this->assertDatabaseMissing('assets', ['isin' => 'NO0012345678']);
        $this->assertDatabaseHas('variables', ['name' => 'isin_counter', 'value' => '4']);
    }

    public function test_lookup_isin_does_not_silently_overwrite_manual_values(): void
    {
        config(['services.eodhd.token' => 'test-token']);
        $this->setLookupCounter();

        Http::fake([
            'https://eodhd.com/api/search/*' => Http::response([
                ['ISIN' => 'NO0012345678', 'Code' => 'LOOKUP', 'Country' => 'Norway', 'Name' => 'Lookup Name', 'Type' => 'Fund', 'isPrimary' => true],
            ]),
        ]);

        Livewire::test('asset-widget')
            ->set('isin', 'NO0012345678')
            ->set('ticker', 'MANUAL')
            ->set('country', 'SE')
            ->set('name', 'Manual Name')
            ->set('type', 'Bank')
            ->call('lookupIsin')
            ->assertSet('ticker', 'MANUAL')
            ->assertSet('country', 'SE')
            ->assertSet('name', 'Manual Name')
            ->assertSet('type', 'Bank');
    }

    public function test_lookup_isin_shows_choice_list_for_multiple_plausible_results(): void
    {
        config(['services.eodhd.token' => 'test-token']);
        $this->setLookupCounter();

        Http::fake([
            'https://eodhd.com/api/search/*' => Http::response([
                ['ISIN' => 'NO0012345678', 'Code' => 'AAA', 'Country' => 'Norway', 'Name' => 'Choice A', 'Type' => 'Common Stock'],
                ['ISIN' => 'NO0012345678', 'Code' => 'BBB', 'Country' => 'Sweden', 'Name' => 'Choice B', 'Type' => 'Fund'],
            ]),
        ]);

        Livewire::test('asset-widget')
            ->set('showForm', true)
            ->set('isin', 'NO0012345678')
            ->call('lookupIsin')
            ->assertSet('lookupMessage', 'Choose a lookup result.')
            ->assertSee('data-isin-lookup-choices')
            ->assertSee('Choice A')
            ->assertSee('Choice B');
    }

    public function test_lookup_isin_shows_no_match_message(): void
    {
        config(['services.eodhd.token' => 'test-token']);
        $this->setLookupCounter();

        Http::fake([
            'https://eodhd.com/api/search/*' => Http::response([]),
        ]);

        Livewire::test('asset-widget')
            ->set('isin', 'NO0012345678')
            ->call('lookupIsin')
            ->assertSet('lookupMessage', 'No match found.');
    }
}
