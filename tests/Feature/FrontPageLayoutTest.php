<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrontPageLayoutTest extends TestCase
{
    use RefreshDatabase;
    public function test_front_page_shows_top_placeholder_cards(): void
    {
        $response = $this->get('/');

        $response->assertSee('data-top-card="top01"', false);
        $response->assertSee('top08');
        $response->assertSee('Instance Info');
    }

    public function test_top01_identity_card_matches_spec(): void
    {
        $response = $this->get('/');

        $response->assertSee('Values and assets');
        $response->assertSee('Historic data');
        $response->assertSee('data-top01-purple-line', false);
        $response->assertSee('width: 24mm; height: 3mm; background: #6f42c1;', false);
        $response->assertSee('min-height: 200px;', false);
        $response->assertSee('shadow-sm border-0', false);
    }

    public function test_utility_cards_use_standard_header_style(): void
    {
        $response = $this->get('/');

        $this->assertSame(6, substr_count($response->getContent(), 'data-utility-card-header'));
        $response->assertSee('card-header bg-white border-bottom', false);
        $response->assertSee('mb-0 fw-semibold small', false);

        foreach (['bi-square', 'bi-info-circle', 'bi-sliders', 'bi-clock', 'bi-git', 'bi-server'] as $icon) {
            $response->assertSee($icon);
        }
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
        $response->assertSee('data-card-toggle-control="variables"', false);
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
        $response->assertSee('data-card-toggle="variables"', false);
    }

    public function test_card_selector_has_separate_workbench_and_variables_controls(): void
    {
        $response = $this->get('/');

        $response->assertSee('data-card-toggle-control="workbench"', false);
        $response->assertSee('data-card-toggle-control="variables"', false);
        $response->assertSee('data-card-toggle="workbench"', false);
        $response->assertSee('data-card-toggle="variables"', false);
        $response->assertSeeInOrder(['data-card-toggle="workbench"', 'data-card-toggle="variables"'], false);
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

        $response->assertSee('tab01');
        $response->assertSee('Assets');
        $response->assertSee('Bundles');
        $response->assertSee('Areas');
        $response->assertSee('Holdings');
        $response->assertSee('History');
        $response->assertSee('tab07');
        $response->assertSee('tab08');
        $response->assertSee('tab09');
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

        foreach ([1, 5, 6, 7, 8, 9, 10] as $i) {
            $num = str_pad((string) $i, 2, '0', STR_PAD_LEFT);
            $response->assertSeeText("info{$num}");
        }
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

    public function test_variables_widget_appears_below_workbench_tabs(): void
    {
        $response = $this->get('/');

        $response->assertSeeInOrder(['id="workbenchTabContent"', 'data-workbench-bottom-widget="variables"'], false);
    }
}
