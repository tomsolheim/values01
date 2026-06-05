<?php

namespace Tests\Feature;

use App\Models\Variable;
use App\Services\SystemStatusVariables;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class VariablesWidgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_variables_table_exists_with_expected_columns(): void
    {
        $columns = Schema::getColumnListing('variables');

        foreach (['id', 'name', 'value', 'group', 'comment', 'created_at', 'updated_at'] as $column) {
            $this->assertContains($column, $columns);
        }
    }

    public function test_initial_vmware_cores_variable_exists(): void
    {
        $this->assertDatabaseHas('variables', [
            'name' => 'vmware_cores',
            'value' => '0',
            'group' => 'system',
        ]);
    }

    public function test_system_status_variables_reads_vmware_cores(): void
    {
        Variable::where('name', 'vmware_cores')->update(['value' => '4']);

        $this->assertSame(4, app(SystemStatusVariables::class)->vmwareCores());
    }

    public function test_system_status_variables_falls_back_when_vmware_cores_is_missing(): void
    {
        Variable::where('name', 'vmware_cores')->delete();

        $this->assertSame(0, app(SystemStatusVariables::class)->vmwareCores());
    }

    public function test_can_create_variable_with_required_fields(): void
    {
        Variable::create([
            'name' => 'runtime_mode',
            'value' => 'local',
            'group' => 'runtime',
            'comment' => 'Runtime mode.',
        ]);

        $this->assertDatabaseHas('variables', ['name' => 'runtime_mode', 'value' => 'local']);
    }

    public function test_variables_widget_validates_required_fields(): void
    {
        Livewire::test('variables-widget')
            ->call('create')
            ->assertHasErrors(['name', 'value', 'group', 'comment']);
    }

    public function test_can_update_variable(): void
    {
        $variable = Variable::create([
            'name' => 'runtime_mode',
            'value' => 'local',
            'group' => 'runtime',
            'comment' => 'Runtime mode.',
        ]);

        $variable->update(['value' => 'production']);

        $this->assertDatabaseHas('variables', ['name' => 'runtime_mode', 'value' => 'production']);
    }

    public function test_can_delete_variable(): void
    {
        $variable = Variable::create([
            'name' => 'delete_me',
            'value' => '1',
            'group' => 'runtime',
            'comment' => 'Delete test.',
        ]);

        $variable->delete();

        $this->assertDatabaseMissing('variables', ['name' => 'delete_me']);
    }

    public function test_front_page_shows_variables_widget_below_tabbed_workbench(): void
    {
        $response = $this->get('/');

        $response->assertSee('data-workbench-bottom-widget="variables"', false);
        $response->assertSee('data-variables-widget', false);
        $response->assertSee('Variables');
        $response->assertSee('Search variables');
    }

    public function test_variables_widget_is_not_a_workbench_tab(): void
    {
        $response = $this->get('/');

        $response->assertDontSee('id="variables-tab"', false);
        $response->assertDontSee('data-bs-target="#variables"', false);
    }

    public function test_variables_list_shows_expected_columns_and_initial_variable(): void
    {
        $response = $this->get('/');

        foreach (['Name', 'Value', 'Group', 'Comment', 'vmware_cores', 'system'] as $text) {
            $response->assertSee($text);
        }
    }

    public function test_variables_list_shows_edit_and_delete_icons(): void
    {
        $response = $this->get('/');

        $response->assertSee('bi-pen');
        $response->assertSee('bi-trash');
        $response->assertSee('Edit');
        $response->assertSee('Delete');
    }

    public function test_variables_list_is_paginated(): void
    {
        for ($i = 0; $i < 15; $i++) {
            Variable::create([
                'name' => "variable_{$i}",
                'value' => (string) $i,
                'group' => 'test',
                'comment' => 'Pagination test.',
            ]);
        }

        $response = $this->get('/');

        $response->assertSee('pagination-sm');
        $response->assertSee('page-item');
        $response->assertSee('page-link');
    }

    public function test_variables_widget_has_csv_import_and_export_controls(): void
    {
        $response = $this->get('/');

        $response->assertSee('variables-csv-import');
        $response->assertSee('Import CSV');
        $response->assertSee('Export CSV');
    }
}
