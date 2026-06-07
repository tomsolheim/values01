<?php

namespace Tests\Feature;

use App\Models\Link;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class LinksWidgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_links_table_exists_with_expected_columns(): void
    {
        $columns = Schema::getColumnListing('links');

        foreach (['id', 'name', 'group', 'url', 'comment', 'created_at', 'updated_at'] as $column) {
            $this->assertContains($column, $columns);
        }
    }

    public function test_links_widget_validates_required_fields(): void
    {
        Livewire::test('links-widget')
            ->call('create')
            ->assertHasErrors(['name', 'url']);
    }

    public function test_links_widget_rejects_unsupported_url_schemes(): void
    {
        Livewire::test('links-widget')
            ->set('name', 'FTP Link')
            ->set('url', 'ftp://example.com/file')
            ->call('create')
            ->assertHasErrors(['url']);

        Livewire::test('links-widget')
            ->set('name', 'Bad Link')
            ->set('url', 'not-a-url')
            ->call('create')
            ->assertHasErrors(['url']);
    }

    public function test_links_widget_can_create_update_and_delete_links(): void
    {
        Livewire::test('links-widget')
            ->set('name', 'Laravel')
            ->set('group', 'Docs')
            ->set('url', 'https://laravel.com')
            ->set('comment', 'Framework docs')
            ->call('create');

        $link = Link::where('name', 'Laravel')->firstOrFail();

        Livewire::test('links-widget')
            ->call('edit', $link->id)
            ->assertSet('showForm', true)
            ->set('name', 'Laravel Docs')
            ->set('url', 'https://laravel.com/docs')
            ->call('update');

        $this->assertDatabaseHas('links', [
            'name' => 'Laravel Docs',
            'group' => 'Docs',
            'url' => 'https://laravel.com/docs',
            'comment' => 'Framework docs',
        ]);

        Livewire::test('links-widget')
            ->call('delete', $link->id);

        $this->assertDatabaseMissing('links', ['id' => $link->id]);
    }

    public function test_links_widget_broad_search_covers_all_list_fields(): void
    {
        Link::create(['name' => 'Name Match', 'group' => 'Alpha', 'url' => 'https://name.example.com', 'comment' => 'One']);
        Link::create(['name' => 'Second', 'group' => 'Group Match', 'url' => 'https://second.example.com', 'comment' => 'Two']);
        Link::create(['name' => 'Third', 'group' => 'Gamma', 'url' => 'https://url-match.example.com', 'comment' => 'Three']);
        Link::create(['name' => 'Fourth', 'group' => 'Delta', 'url' => 'https://fourth.example.com', 'comment' => 'Comment Match']);

        Livewire::test('links-widget')
            ->set('search', 'Match')
            ->assertSee('Name Match')
            ->assertSee('Group Match')
            ->assertSee('url-match.example.com')
            ->assertSee('Comment Match');
    }

    public function test_links_widget_group_filter_uses_distinct_non_empty_groups(): void
    {
        Link::create(['name' => 'One', 'group' => 'Docs', 'url' => 'https://one.example.com']);
        Link::create(['name' => 'Two', 'group' => 'Docs', 'url' => 'https://two.example.com']);
        Link::create(['name' => 'Three', 'group' => 'Tools', 'url' => 'https://three.example.com']);
        Link::create(['name' => 'Ungrouped', 'group' => null, 'url' => 'https://ungrouped.example.com']);

        Livewire::test('links-widget')
            ->assertSee('All groups')
            ->assertSee('Docs')
            ->assertSee('Tools')
            ->set('groupFilter', 'Tools')
            ->assertSee('Three')
            ->assertDontSee('One')
            ->assertDontSee('Ungrouped');
    }

    public function test_links_widget_search_and_group_filter_work_together(): void
    {
        Link::create(['name' => 'Laravel Docs', 'group' => 'Docs', 'url' => 'https://laravel.com']);
        Link::create(['name' => 'Laravel Tool', 'group' => 'Tools', 'url' => 'https://tool.example.com']);

        Livewire::test('links-widget')
            ->set('search', 'Laravel')
            ->set('groupFilter', 'Docs')
            ->assertSee('Laravel Docs')
            ->assertDontSee('Laravel Tool');
    }

    public function test_links_widget_reset_search_clears_search_and_group_filter(): void
    {
        Link::create(['name' => 'Laravel Docs', 'group' => 'Docs', 'url' => 'https://laravel.com']);
        Link::create(['name' => 'Tool Link', 'group' => 'Tools', 'url' => 'https://tool.example.com']);

        Livewire::test('links-widget')
            ->set('search', 'Laravel')
            ->set('groupFilter', 'Docs')
            ->call('resetSearch')
            ->assertSet('search', '')
            ->assertSet('groupFilter', '')
            ->assertSee('Laravel Docs')
            ->assertSee('Tool Link');
    }

    public function test_links_render_safe_new_tab_url_links(): void
    {
        Link::create(['name' => 'Example', 'group' => 'Docs', 'url' => 'https://example.com', 'comment' => 'External link']);

        Livewire::test('links-widget')
            ->assertSee('href="https://example.com"', false)
            ->assertSee('target="_blank"', false)
            ->assertSee('rel="noopener noreferrer"', false);
    }

    public function test_links_widget_has_csv_import_and_export_controls(): void
    {
        $response = $this->get('/');

        $response->assertSee('links-csv-import');
        $response->assertSee('Import CSV');
        $response->assertSee('Export CSV');
    }
}
