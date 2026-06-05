<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Asset;
use App\Models\Bundle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AssetWidgetTest extends TestCase
{
    use RefreshDatabase;

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
}
