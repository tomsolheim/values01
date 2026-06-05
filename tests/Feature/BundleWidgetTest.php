<?php

namespace Tests\Feature;

use App\Models\Bundle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BundleWidgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_bundles_table_exists_with_expected_columns(): void
    {
        $columns = Schema::getColumnListing('bundles');

        $this->assertContains('id', $columns);
        $this->assertContains('name', $columns);
        $this->assertContains('comment', $columns);
        $this->assertContains('created_at', $columns);
        $this->assertContains('updated_at', $columns);
    }

    public function test_can_create_bundle_with_required_fields(): void
    {
        Bundle::create(['name' => 'Test Bundle']);

        $this->assertDatabaseHas('bundles', ['name' => 'Test Bundle']);
    }

    public function test_can_create_bundle_with_comment(): void
    {
        Bundle::create([
            'name' => 'Test Bundle',
            'comment' => 'A test comment',
        ]);

        $this->assertDatabaseHas('bundles', [
            'name' => 'Test Bundle',
            'comment' => 'A test comment',
        ]);
    }

    public function test_can_update_bundle(): void
    {
        $bundle = Bundle::create(['name' => 'Original Name']);

        $bundle->update(['name' => 'Updated Name']);

        $this->assertDatabaseHas('bundles', ['name' => 'Updated Name']);
        $this->assertDatabaseMissing('bundles', ['name' => 'Original Name']);
    }

    public function test_can_delete_bundle(): void
    {
        $bundle = Bundle::create(['name' => 'Delete Me']);

        $bundle->delete();

        $this->assertDatabaseMissing('bundles', ['name' => 'Delete Me']);
    }

    public function test_front_page_shows_bundle_widget(): void
    {
        $response = $this->get('/');

        $response->assertSee('Bundles');
        $response->assertSee('Show form');
        $response->assertSee('Search bundles');
    }

    public function test_bundle_list_shows_edit_with_pen_icon_and_tooltip(): void
    {
        Bundle::create(['name' => 'Test Bundle']);

        $response = $this->get('/');

        $response->assertSee('bi-pen');
        $response->assertSee('title=');
        $response->assertSee('Edit');
    }

    public function test_bundle_list_shows_delete_with_trash_icon_and_tooltip(): void
    {
        Bundle::create(['name' => 'Test Bundle']);

        $response = $this->get('/');

        $response->assertSee('bi-trash');
        $response->assertSee('title=');
        $response->assertSee('Delete');
    }

    public function test_bundle_list_shows_edit_and_delete_buttons_beside_each_other(): void
    {
        Bundle::create(['name' => 'Test Bundle']);

        $response = $this->get('/');

        $response->assertSee('bi-pen');
        $response->assertSee('bi-trash');
    }

    public function test_bundle_list_is_paginated(): void
    {
        for ($i = 0; $i < 15; $i++) {
            Bundle::create(['name' => "Bundle {$i}"]);
        }

        $response = $this->get('/');

        $response->assertSee('pagination-sm');
    }

    public function test_bundle_pagination_uses_bootstrap_classes(): void
    {
        for ($i = 0; $i < 15; $i++) {
            Bundle::create(['name' => "Bundle {$i}"]);
        }

        $response = $this->get('/');

        $response->assertSee('pagination-sm');
        $response->assertSee('page-item');
        $response->assertSee('page-link');
    }

    public function test_bundle_pagination_has_accessible_labels(): void
    {
        for ($i = 0; $i < 15; $i++) {
            Bundle::create(['name' => "Bundle {$i}"]);
        }

        $response = $this->get('/');

        $response->assertSee('aria-label');
    }

    public function test_bundle_widget_has_csv_import_and_export_controls(): void
    {
        $response = $this->get('/');

        $response->assertSee('bundle-csv-import');
        $response->assertSee('Import CSV');
        $response->assertSee('Export CSV');
    }
}
