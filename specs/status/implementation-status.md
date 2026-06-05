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
| Project foundation | `001-project-start` | `project-start` | `Verified` | Laravel 13 and Livewire 4 are installed. Node/npm are installed. JavaScript dependencies are installed. Vite production build and PHP tests pass. |
| Front page layout | `002-front-page-placeholder-layout` | `front-page-placeholder-layout` | `Partially implemented` | 12-column grid with compact top spacing, top01 identity card left-aligned and not Card Selector-controlled, top08 Table Size live row counts and Instance Info/top09 grouped right with standard utility card headers, sidebar widgets in order (Card Selector, Time, Git Status, System Status) with standard utility card headers, 10-tab workbench, Status tab bundle asset-count list, and Variables widget below the tabbed workbench with a separate Card Selector control. Tab persistence via URL hash. Compact py-3 on main container. |
| Asset widget | `003-asset-table-tab02-widget` | `asset-table-tab02-widget` | `Partially implemented` | Asset table, model, and form/list widget live in tab02. Includes type/country dropdowns, ISIN uniqueness, bundle/area dropdowns, relation names in list, search, pagination, icon actions, and CSV import/export controls. Tested. Follow-up needed: add ISIN lookup button beside Save/Create using configured external lookup provider. |
| Bundle and Area widgets | `004-bundle-area-tab-widgets` | `bundle-area-tab-widgets` | `Verified` | Bundle widget in tab03, Area widget in tab04. Both use pagination-sm, wire:click pagination, icon buttons with tooltips, WithPagination trait, search, and CSV import/export controls. Tested. |
| Holdings widget | `005-holdings-table` | `holdings-table` | `Specified` | Form/list belongs in tab05. Calculations deferred. |
| Transactions import | `006-transactions-table-import` | `transactions-table-import` | `Verified` | Transactions table, UTF-16 tab-separated import service, tab07 Import widget, ISIN filter, duplicate source_id protection, repeated Valuta mapping, decimal normalization, no-ISIN import, and duplicate-independent add-assets function are implemented and tested. |
| History list | `007-history-table-tab06-list` | `history-table-tab06-list` | `Specified` | List belongs in tab06 with Ticker/ISIN selector. Generation rules TBS. |
| Shared CRUD list behavior | `008-shared-crud-list-behavior` | `shared-crud-list-behavior` | `Partially implemented` | Asset, Bundle, Area, and Variables widgets follow pagination-sm, wire:click pagination, icon buttons with tooltips, accessible labels, and right-aligned CSV import/export controls. Holdings not yet implemented. |
| Variables table | `009-variables-table` | `variables-table` | `Verified` | Variables table, initial vmware_cores value, SystemStatusVariables reader/fallback, and bottom-of-workbench Variables CRUD widget are implemented and tested. |

## Current Prototype State

- Laravel project exists at `/tomaco3/htdocs/values01`.
- The current route `/` renders the Bootstrap workbench layout.
- The tabbed workbench includes implemented Asset, Bundle, Area, and Transaction Import widgets.
- The front page includes Instance Info in the top09 position and sidebar widgets for Card Selector, Time, Git Status, and System Status.
- The workbench includes a Variables CRUD widget below the tabbed area.
- JavaScript dependencies are installed and Vite production build is verified.

## Next Recommended Implementation Slice

Build the next prototype slice:

1. Holdings table, model, and tab05 form/list widget.
2. History table, model, and tab06 list widget with Ticker/ISIN selector.
3. Keep holding calculations deferred until formulas are specified.
4. Keep import preview, rollback, and error reporting deferred until those behaviors are specified.
