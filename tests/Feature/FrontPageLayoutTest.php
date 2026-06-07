<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Asset;
use App\Models\Bundle;
use App\Models\Transaction as TransactionModel;
use App\Models\Variable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FrontPageLayoutTest extends TestCase
{
    use RefreshDatabase;
    public function test_front_page_shows_top_placeholder_cards(): void
    {
        $response = $this->get('/');

        $response->assertSee('data-top-card="top01"', false);
        $response->assertSee('Table Size');
        $response->assertSee('Instance Info');
    }

    public function test_top01_identity_card_matches_spec(): void
    {
        $response = $this->get('/');

        $response->assertSee('Values and assets');
        $response->assertSee('Historic data');
        $response->assertSee('data-top01-purple-line', false);
        $response->assertSee('width: 24mm; height: 3mm; background: #6f42c1;', false);
        $response->assertSee('height: 150px;', false);
        $response->assertSee('shadow-sm border-0', false);
    }

    public function test_utility_cards_use_standard_header_style(): void
    {
        $response = $this->get('/');

        $this->assertSame(6, substr_count($response->getContent(), 'data-utility-card-header'));
        $response->assertSee('card-header bg-white border-bottom', false);
        $response->assertSee('mb-0 fw-semibold small', false);

        foreach (['bi-hdd', 'bi-info-circle', 'bi-sliders', 'bi-clock', 'bi-git', 'bi-server'] as $icon) {
            $response->assertSee($icon);
        }
    }

    public function test_top08_table_size_widget_matches_spec(): void
    {
        $response = $this->get('/');

        $response->assertSee('data-table-size-widget', false);
        $response->assertSee('Table Size');
        $response->assertSee('bi-hdd');
        $response->assertSee('data-table-size-counts', false);

        foreach (['Assets', 'Bundles', 'Areas', 'Transactions', 'Variables'] as $label) {
            $response->assertSee($label);
        }

        $response->assertSee('col-6', false);
    }

    public function test_top08_table_size_reads_live_database_counts(): void
    {
        Bundle::create(['name' => 'Count Bundle']);
        Area::create(['name' => 'Count Area']);
        Asset::create(['type' => 'Stock', 'name' => 'Count Asset']);
        TransactionModel::create(['source_id' => 'count-transaction']);
        Variable::create(['name' => 'count_variable', 'value' => '1', 'group' => 'test', 'comment' => 'Count test.']);

        $response = $this->get('/');

        $response->assertSeeInOrder(['Assets', '1']);
        $response->assertSeeInOrder(['Bundles', '1']);
        $response->assertSeeInOrder(['Areas', '1']);
        $response->assertSeeInOrder(['Transactions', '1']);
        $response->assertSeeInOrder(['Variables', '2']);
    }

    public function test_top_area_aligns_top01_left_and_top08_top09_right(): void
    {
        $response = $this->get('/');

        $response->assertSee('data-top-area', false);
        $response->assertSee('data-top-position="left"', false);
        $response->assertSee('data-top-card="top01"', false);
        $response->assertSee('data-top-position="right"', false);
        $response->assertSee('data-top-right-group', false);
        $response->assertSee('justify-content-end', false);
        $response->assertSee('ms-md-auto', false);
        $response->assertSee('data-card-toggle="top08"', false);
        $response->assertSee('data-card-toggle="top09"', false);
    }

    public function test_instance_info_shows_identity_labels(): void
    {
        $response = $this->get('/');

        $response->assertSee('Project');
        $response->assertSee('Hostname');
        $response->assertSee('IP Address');
        $response->assertSee('data-card-toggle="top09"', false);
    }

    public function test_front_page_shows_sidebar_placeholder_cards(): void
    {
        $response = $this->get('/');

        $response->assertSee('Card Selector');
        $response->assertSee('Time');
        $response->assertSee('Git Status');
        $response->assertSee('System Status');
        $response->assertDontSee('side02');
    }

    public function test_sidebar_widgets_appear_in_specified_order(): void
    {
        $response = $this->get('/');

        $response->assertSeeInOrder(['Card Selector', 'Time', 'Git Status', 'System Status']);
    }

    public function test_time_widget_shows_local_and_utc_time_fields(): void
    {
        $response = $this->get('/');

        $response->assertSee('Local time');
        $response->assertSee('UTC time');
        $response->assertSee('data-local-time-widget', false);
        $response->assertSee('data-card-toggle="time"', false);
    }

    public function test_git_status_widget_shows_expected_fields(): void
    {
        $response = $this->get('/');

        foreach (['Current commit hash', 'Last commit date', 'Local pending changes', 'Branch', 'Upstream', 'Remote commit', 'Ahead/behind sync status'] as $label) {
            $response->assertSee($label);
        }

        $response->assertSee('data-card-toggle="git-status"', false);
    }

    public function test_system_status_widget_shows_expected_fields(): void
    {
        $response = $this->get('/');

        foreach (['CPUs', 'VM CPUs', 'CPU Load', 'Memory', 'Free Memory', 'Disk', 'Free Disk', 'Used Disk', 'Last Boot'] as $label) {
            $response->assertSee($label);
        }

        $response->assertSee('data-card-toggle="system-status"', false);
    }

    public function test_card_selector_has_expected_controls(): void
    {
        $response = $this->get('/');

        $response->assertSee('All on');
        $response->assertSee('All off');
        $response->assertSee('Toggle cards');
        $response->assertSee('data-card-selector-position="side01"', false);
        $response->assertDontSee('data-card-toggle-control="top01"', false);
        $response->assertSee('data-card-toggle-control="top08"', false);
        $response->assertSee('data-card-toggle-control="top09"', false);
        $response->assertSee('data-card-toggle-control="time"', false);
        $response->assertSee('data-card-toggle-control="git-status"', false);
        $response->assertSee('data-card-toggle-control="system-status"', false);
        $response->assertDontSee('data-card-toggle-control="side02"', false);
        $response->assertSee('data-card-toggle-control="workbench"', false);
        $response->assertDontSee('data-card-toggle-control="variables"', false);
    }

    public function test_card_selector_targets_registered_visible_panels(): void
    {
        $response = $this->get('/');

        $response->assertDontSee('data-card-toggle="top01"', false);
        $response->assertSee('data-card-toggle="top08"', false);
        $response->assertSee('data-card-toggle="top09"', false);
        $response->assertSee('data-card-toggle="time"', false);
        $response->assertSee('data-card-toggle="git-status"', false);
        $response->assertSee('data-card-toggle="system-status"', false);
        $response->assertDontSee('data-card-toggle="side02"', false);
        $response->assertSee('data-card-toggle="workbench"', false);
        $response->assertDontSee('data-card-toggle="variables"', false);
    }

    public function test_card_selector_controls_variables_through_workbench_only(): void
    {
        $response = $this->get('/');

        $response->assertSee('data-card-toggle-control="workbench"', false);
        $response->assertSee('data-card-toggle="workbench"', false);
        $response->assertDontSee('data-card-toggle-control="variables"', false);
        $response->assertDontSee('data-card-toggle="variables"', false);
    }

    public function test_card_selector_does_not_control_top01(): void
    {
        $response = $this->get('/');

        $response->assertSee('top01');
        $response->assertDontSee('data-card-toggle="top01"', false);
        $response->assertDontSee('data-card-toggle-control="top01"', false);
    }

    public function test_front_page_shows_visible_tab_labels(): void
    {
        $response = $this->get('/');

        $response->assertSee('Status');
        $response->assertSee('Assets');
        $response->assertSee('Bundles');
        $response->assertSee('Areas');
        $response->assertSee('Holdings');
        $response->assertSee('History');
        $response->assertSee('Import');
        $response->assertSee('Links');
        $response->assertSee('Variables');
        $response->assertSee('tab10');
    }

    public function test_front_page_shows_stable_tab_ids(): void
    {
        $response = $this->get('/');

        foreach (['tab01', 'tab02', 'tab03', 'tab04', 'tab05', 'tab06', 'tab07', 'tab08', 'tab09', 'tab10'] as $id) {
            $response->assertSee($id);
        }
    }

    public function test_placeholder_tabs_show_matching_info_content(): void
    {
        $response = $this->get('/');

        foreach ([5, 6, 10] as $i) {
            $num = str_pad((string) $i, 2, '0', STR_PAD_LEFT);
            $response->assertSeeText("info{$num}");
        }

        $response->assertDontSeeText('info08');
        $response->assertDontSeeText('info09');
    }

    public function test_tab01_shows_status_widget(): void
    {
        $response = $this->get('/');

        $response->assertSee('data-status-widget', false);
        $response->assertSee('data-status-update-button', false);
        $response->assertSee('data-status-bundle-counts', false);
        $response->assertSee('Bundle');
        $response->assertSee('Assets');
    }

    public function test_status_update_button_refreshes_bundle_asset_counts(): void
    {
        $bundle = Bundle::create(['name' => 'Refresh Bundle']);

        $component = Livewire::test('status-widget')
            ->assertSee('Refresh Bundle')
            ->assertSee('0');

        Asset::create(['type' => 'Stock', 'name' => 'Refresh Asset', 'bundle_id' => $bundle->id]);

        $component
            ->call('$refresh')
            ->assertSee('Refresh Bundle')
            ->assertSee('1');
    }

    public function test_status_tab_shows_bundle_asset_counts_including_zero(): void
    {
        $empty = Bundle::create(['name' => 'Empty Bundle']);
        $filled = Bundle::create(['name' => 'Filled Bundle']);
        Asset::create(['type' => 'Stock', 'name' => 'Asset One', 'bundle_id' => $filled->id]);
        Asset::create(['type' => 'Fund', 'name' => 'Asset Two', 'bundle_id' => $filled->id]);

        $response = $this->get('/');

        $response->assertSeeInOrder(['Empty Bundle', '0']);
        $response->assertSeeInOrder(['Filled Bundle', '2']);
    }

    public function test_status_tab_assets_column_header_and_counts_are_centered(): void
    {
        $bundle = Bundle::create(['name' => 'Centered Bundle']);
        Asset::create(['type' => 'Stock', 'name' => 'Asset One', 'bundle_id' => $bundle->id]);

        $response = $this->get('/');

        $response->assertSee('<th class="text-center">Assets</th>', false);
        $response->assertSee('<td class="text-center fw-semibold">1</td>', false);
    }

    public function test_status_tab_shows_bundle_comments_margin_and_bundle_links(): void
    {
        $bundle = Bundle::create(['name' => 'Linked Bundle', 'comment' => 'Shown to the right']);

        $response = $this->get('/');

        $response->assertSee('mx-md-5', false);
        $response->assertSee('data-status-bundle-link="'.$bundle->id.'"', false);
        $response->assertSee('values01OpenAssetsTabFromStatus', false);
        $response->assertSeeInOrder(['Linked Bundle', '0', 'Shown to the right']);
    }

    public function test_status_to_assets_navigation_activates_tab_hash_and_bundle_filter_focus(): void
    {
        Bundle::create(['name' => 'Navigation Bundle']);

        $response = $this->get('/');

        $response->assertSee('window.values01OpenAssetsTabFromStatus', false);
        $response->assertSee("document.getElementById('tab02-tab')", false);
        $response->assertSee("window.bootstrap.Tab.getOrCreateInstance(assetsTab).show()", false);
        $response->assertSee("history.replaceState(null, '', '#tab02')", false);
        $response->assertSee("document.querySelector('[data-asset-bundle-filter]')", false);
        $response->assertSee('bundleFilter.focus()', false);
    }

    public function test_status_bundle_action_dispatches_asset_filter_event(): void
    {
        $bundle = Bundle::create(['name' => 'Dispatch Bundle']);

        Livewire::test('status-widget')
            ->call('selectBundle', $bundle->id)
            ->assertDispatched('status-bundle-selected', bundleId: (string) $bundle->id);
    }

    public function test_tab07_shows_transaction_import_widget(): void
    {
        $response = $this->get('/');

        $response->assertSee('Import');
        $response->assertSee('Transaction Import');
        $response->assertSee('All ISINs');
        $response->assertSee('One ISIN');
        $response->assertSee('Selected ISIN');
        $response->assertSee('Choose existing asset ISIN');
        $response->assertSee('ISIN filter');
        $response->assertSee('Add assets');
        $response->assertSee('Transactions: 0');
        $response->assertSee('Refresh');
        $response->assertSee('Delete all transactions');
    }

    public function test_tab02_shows_asset_widget(): void
    {
        $response = $this->get('/');

        $response->assertSee('Assets');
        $response->assertSee('Show form');
        $response->assertSee('Search assets');
    }

    public function test_tab04_shows_area_widget(): void
    {
        $response = $this->get('/');

        $response->assertSee('Areas');
        $response->assertSee('Show form');
    }

    public function test_tab03_shows_bundle_widget(): void
    {
        $response = $this->get('/');

        $response->assertSee('Bundles');
        $response->assertSee('Show form');
        $response->assertSee('Name');
        $response->assertSee('Comment');
    }

    public function test_tab08_shows_links_widget(): void
    {
        $response = $this->get('/');

        $response->assertSee('Links');
        $response->assertSee('data-links-widget', false);
        $response->assertSee('Show form');
        $response->assertSee('Search links');
        $response->assertSee('All groups');
        $response->assertSee('Reset Search');
        $response->assertSee('Import CSV');
        $response->assertSee('Export CSV');
    }

    public function test_tab_persistence_js_is_present(): void
    {
        $response = $this->get('/');

        $response->assertSee('shown.bs.tab');
        $response->assertSee('history.replaceState');
        $response->assertSee('data-bs-target');
    }

    public function test_tab_persistence_restores_from_hash_on_load(): void
    {
        $response = $this->get('/');

        $response->assertSee('location.hash');
        $response->assertSee('getOrCreateInstance');
    }

    public function test_variables_widget_appears_inside_tab09(): void
    {
        $response = $this->get('/');

        $response->assertSee('id="tab09"', false);
        $response->assertSee('id="tab09-tab"', false);
        $response->assertSeeInOrder(['id="tab09"', 'data-variables-widget'], false);
        $response->assertDontSee('data-workbench-bottom-widget="variables"', false);
    }
}
