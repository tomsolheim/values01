<?php

namespace Tests\Feature;

use App\Models\Area;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AreaWidgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_areas_table_exists_with_expected_columns(): void
    {
        $columns = Schema::getColumnListing('areas');

        $this->assertContains('id', $columns);
        $this->assertContains('name', $columns);
        $this->assertContains('comment', $columns);
        $this->assertContains('created_at', $columns);
        $this->assertContains('updated_at', $columns);
    }

    public function test_can_create_area_with_required_fields(): void
    {
        Area::create(['name' => 'Test Area']);

        $this->assertDatabaseHas('areas', ['name' => 'Test Area']);
    }

    public function test_can_create_area_with_comment(): void
    {
        Area::create([
            'name' => 'Test Area',
            'comment' => 'A test comment',
        ]);

        $this->assertDatabaseHas('areas', [
            'name' => 'Test Area',
            'comment' => 'A test comment',
        ]);
    }

    public function test_can_update_area(): void
    {
        $area = Area::create(['name' => 'Original Name']);

        $area->update(['name' => 'Updated Name']);

        $this->assertDatabaseHas('areas', ['name' => 'Updated Name']);
        $this->assertDatabaseMissing('areas', ['name' => 'Original Name']);
    }

    public function test_can_delete_area(): void
    {
        $area = Area::create(['name' => 'Delete Me']);

        $area->delete();

        $this->assertDatabaseMissing('areas', ['name' => 'Delete Me']);
    }

    public function test_front_page_shows_area_widget(): void
    {
        $response = $this->get('/');

        $response->assertSee('Areas');
        $response->assertSee('Show form');
        $response->assertSee('Search areas');
    }

    public function test_area_list_shows_edit_with_pen_icon_and_tooltip(): void
    {
        Area::create(['name' => 'Test Area']);

        $response = $this->get('/');

        $response->assertSee('bi-pen');
        $response->assertSee('title=');
        $response->assertSee('Edit');
    }

    public function test_area_list_shows_delete_with_trash_icon_and_tooltip(): void
    {
        Area::create(['name' => 'Test Area']);

        $response = $this->get('/');

        $response->assertSee('bi-trash');
        $response->assertSee('title=');
        $response->assertSee('Delete');
    }

    public function test_area_list_shows_edit_and_delete_buttons_beside_each_other(): void
    {
        Area::create(['name' => 'Test Area']);

        $response = $this->get('/');

        $response->assertSee('bi-pen');
        $response->assertSee('bi-trash');
    }

    public function test_area_list_is_paginated(): void
    {
        for ($i = 0; $i < 15; $i++) {
            Area::create(['name' => "Area {$i}"]);
        }

        $response = $this->get('/');

        $response->assertSee('pagination-sm');
    }

    public function test_area_pagination_uses_bootstrap_classes(): void
    {
        for ($i = 0; $i < 15; $i++) {
            Area::create(['name' => "Area {$i}"]);
        }

        $response = $this->get('/');

        $response->assertSee('pagination-sm');
        $response->assertSee('page-item');
        $response->assertSee('page-link');
    }

    public function test_area_pagination_has_accessible_labels(): void
    {
        for ($i = 0; $i < 15; $i++) {
            Area::create(['name' => "Area {$i}"]);
        }

        $response = $this->get('/');

        $response->assertSee('aria-label');
    }

    public function test_area_widget_has_csv_import_and_export_controls(): void
    {
        $response = $this->get('/');

        $response->assertSee('area-csv-import');
        $response->assertSee('Import CSV');
        $response->assertSee('Export CSV');
    }
}
