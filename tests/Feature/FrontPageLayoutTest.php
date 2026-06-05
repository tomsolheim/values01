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

        $response->assertSee('top01');
        $response->assertSee('top08');
        $response->assertSee('Instance Info');
    }

    public function test_top_area_aligns_top01_left_and_top08_top09_right(): void
    {
        $response = $this->get('/');

        $response->assertSee('data-top-area', false);
        $response->assertSee('data-top-position="left"', false);
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
        $response->assertSee('side02');
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
        $response->assertSee('data-card-toggle-control="side02"', false);
        $response->assertSee('data-card-toggle-control="workbench"', false);
    }

    public function test_card_selector_targets_registered_visible_panels(): void
    {
        $response = $this->get('/');

        $response->assertDontSee('data-card-toggle="top01"', false);
        $response->assertSee('data-card-toggle="top08"', false);
        $response->assertSee('data-card-toggle="top09"', false);
        $response->assertSee('data-card-toggle="side02"', false);
        $response->assertSee('data-card-toggle="workbench"', false);
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
}
