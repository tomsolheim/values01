# Implementation Status

This file tracks what is currently implemented. It does not replace the specs.

Status values:

- `Not started`
- `Specified`
- `Partially implemented`
- `Implemented`
- `Verified`
- `Deferred`
- `Blocked`

## Feature Status

| Feature | Spec | Acceptance | Status | Notes |
| --- | --- | --- | --- | --- |
| Project foundation | `001-project-start` | `project-start` | `Verified` | Laravel 13 and Livewire 4 are installed. Basic app test passes. Bootstrap is specified and available by CDN fallback until npm is installed. |
| Front page layout | `002-front-page-placeholder-layout` | `front-page-placeholder-layout` | `Partially implemented` | 12-column grid with compact top spacing, top01 left-aligned and not Card Selector-controlled, top08 and Instance Info/top09 grouped right, Card Selector in side01, side02 placeholder, and 10-tab workbench. Tab persistence via URL hash. Compact py-3 on main container. |
| Asset widget | `003-asset-table-tab02-widget` | `asset-table-tab02-widget` | `Verified` | Asset table, model, and form/list widget live in tab02. Includes type/country dropdowns, ISIN uniqueness, bundle/area dropdowns, relation names in list, search, pagination, icon actions, and CSV import/export controls. Tested. |
| Bundle and Area widgets | `004-bundle-area-tab-widgets` | `bundle-area-tab-widgets` | `Verified` | Bundle widget in tab03, Area widget in tab04. Both use pagination-sm, wire:click pagination, icon buttons with tooltips, WithPagination trait, search, and CSV import/export controls. Tested. |
| Holdings widget | `005-holdings-table` | `holdings-table` | `Specified` | Form/list belongs in tab05. Calculations deferred. |
| Transactions import | `006-transactions-table-import` | `transactions-table-import` | `Specified` | Uses ISIN filter because source file has no ticker. Widget tab is TBS. |
| History list | `007-history-table-tab06-list` | `history-table-tab06-list` | `Specified` | List belongs in tab06 with Ticker/ISIN selector. Generation rules TBS. |
| Shared CRUD list behavior | `008-shared-crud-list-behavior` | `shared-crud-list-behavior` | `Partially implemented` | Asset, Bundle, and Area widgets follow pagination-sm, wire:click pagination, icon buttons with tooltips, accessible labels, and right-aligned CSV import/export controls. Holdings not yet implemented. |

## Current Prototype State

- Laravel project exists at `/tomaco3/htdocs/values01`.
- The current route `/` renders the Bootstrap workbench layout.
- The tabbed workbench includes implemented Asset, Bundle, and Area widgets.
- The front page includes Instance Info in the top09 position and Card Selector in the side01 position.
- JavaScript package installation is pending because `npm` is not available on PATH.

## Next Recommended Implementation Slice

Build prototype version 0.1:

1. Front page 12-column layout.
2. Workbench tabs with visible labels.
3. Basic migrations for specified tables.
4. Basic form/list widgets for Assets, Bundles, Areas, Holdings, and History.
5. No holding calculations.
6. No final transaction import workflow.
