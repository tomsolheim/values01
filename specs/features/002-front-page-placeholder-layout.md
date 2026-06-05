# Feature 002: Front Page Placeholder Layout

## Goal

Define the initial front page layout using a 12-column Bootstrap grid with top cards, sidebar widgets, and tabbed workspace content.

## User Story

As a project owner, I want the front page divided into clear placeholder regions so the application can later grow into a structured working interface.

## Layout

The front page uses a 12-column grid.

### Header

- The page has a compact header area with minimal whitespace before the top cards.
- The `<main>` container uses `py-3` (not `py-5`) to keep top padding compact.
- The top cards row uses `mb-3` (not `mb-4`) to reduce gap before the main area.

### Top Area

- The top area spans all 12 grid columns.
- The top area initially contains three single-box placeholder cards.
- The cards are named:
  - `top01`
  - `top08`
  - `top09`
- `top08` is replaced by the Table Size widget.
- `top09` is replaced by the Instance Info widget.
- `top01` is always aligned to the left side of the top area.
- `top08` and `top09` are always aligned to the right side of the top area.
- `top08` and `top09` should appear as a right-side group.
- The spacing between `top08` and `top09` should be compact and consistent with the grid gap.
- The layout may stack on small screens, but desktop layout preserves left/right alignment.

### Top01 Identity Card

- `top01` is an identity card.
- `top01` uses a height of `150px`.
- The card title is `Values and assets`.
- The card subtitle is `Historic data`.
- The card has a purple line.
- The line uses width `24mm` and height `3mm`.
- The card should keep the same general visual structure:
  - White Bootstrap card
  - Soft shadow
  - No visible border
  - Title near the top
  - Subtitle below the title
  - Short colored line below the subtitle

### Instance Info

- The Instance Info widget appears in the `top09` position.
- `top09` follows the standard utility card header style.
- The widget shows app/runtime identity information.
- Initial fields:
  - Project
  - Hostname
  - IP Address
- The widget should adapt visually to the Values01 Bootstrap 5 layout.

### Table Size

- The Table Size widget appears in the `top08` position.
- `top08` follows the standard utility card header style.
- The widget heading is `Table Size`.
- The purpose icon is a disk/storage icon.
- The widget shows row counts for key tables:
  - Assets
  - Bundles
  - Areas
  - Transactions
  - Variables
- The row-count display should use two compact columns so the content fits inside the current top-card height.
- Each count item shows a human-readable table label and the current number of rows.
- Counts should be read from the live database tables.
- If a table is unavailable during early implementation or testing, the widget should fail softly and show a neutral value such as `0` or `-`, rather than breaking the page.
- The widget should adapt visually to the Values01 Bootstrap 5 layout.

### Main Area

Below the top area, the page is split into:

- Sidebar: 3 grid columns wide
- Workbench: 9 grid columns wide

### Sidebar

- The sidebar contains reusable widgets.
- `side01` is replaced by the Card Selector widget.
- The sidebar also includes the following widgets:
  - Time
  - Git Status
  - System Status

### Standard Utility Card Header

This header style applies to:

- All sidebar cards
- `top08`
- `top09`

Rules:

- Each card has a title/header area at the top.
- The title/header area has a thin line at the bottom.
- The card heading uses bold text.
- The card heading uses the same font family and font size across all cards using this style.
- A small purpose icon appears before the heading.
- The icon should visually indicate the purpose of the card.
- The icon and heading should be aligned on the same baseline.
- Header spacing should be compact and consistent across cards.
- The title/header area should not make cards feel heavy or oversized.

Initial purpose icons:

| Card | Suggested Purpose Icon |
| --- | --- |
| `top08` / Table Size | Disk or storage icon |
| `top09` / Instance Info | Info or server identity icon |
| Card Selector | Sliders or controls icon |
| Time | Clock icon |
| Git Status | Git branch or Git icon |
| System Status | Server or activity icon |
- The sidebar widget order is:
  - Card Selector
  - Time
  - Git Status
  - System Status

### Card Selector

- The Card Selector widget appears in the `side01` position.
- The widget controls the visibility of registered cards or widget panels.
- `top01` is not controlled by the Card Selector.
- `top01` should remain visible regardless of Card Selector state.
- The widget includes:
  - A heading identifying the selector.
  - An `All on` action.
  - An `All off` action.
  - A dropdown or compact list of checkboxes for individual cards.
- Each controlled card or widget panel uses a stable card id.
- Only cards explicitly registered with the Card Selector are affected by it.
- The tabbed workbench card and the Variables card are separate Card Selector entries.
- The tabbed workbench card uses its own stable card id.
- The Variables card uses its own stable card id.
- Toggling the tabbed workbench card must not toggle the Variables card.
- Toggling the Variables card must not toggle the tabbed workbench card.
- Card visibility controls should stay in sync with the visible cards.
- The widget should adapt visually to the Values01 Bootstrap 5 layout.
- The exact list of controlled cards is TBS before final implementation.

### Time

- The Time widget appears in the sidebar.
- The widget shows:
  - Local time
  - UTC time
- Time values should refresh automatically without a full page reload.
- The widget should adapt visually to the Values01 Bootstrap 5 layout.

### Git Status

- The Git Status widget appears in the sidebar.
- The widget shows local repository status.
- Initial fields:
  - Current commit hash
  - Last commit date
  - Local pending changes
  - Branch
  - Upstream
  - Remote commit
  - Ahead/behind sync status
- The widget may include a refresh action for remote status.
- The widget should adapt visually to the Values01 Bootstrap 5 layout.

### System Status

- The System Status widget appears in the sidebar.
- The widget shows runtime machine status.
- Initial fields:
  - CPUs
  - VM CPUs or reserved CPUs
  - CPU Load
  - Memory
  - Free Memory
  - Disk
  - Free Disk
  - Used Disk
  - Last Boot
- The widget may refresh automatically or include a manual refresh action.
- The widget uses the `variables` table for configurable runtime values such as reserved CPU count.
- The widget should adapt visually to the Values01 Bootstrap 5 layout.

### Workbench

- The workbench is a tabbed area.
- It contains 10 tabs.
- The stable internal tab ids are `tab01` through `tab10`.
- Each tab has a visible human-readable label.
- The active tab must persist during interactions inside that tab.
- Tab persistence applies to pagination, search, edit, delete, save, cancel, and form show/hide actions.
- Interacting with a widget inside one tab must not reset the workbench back to `tab01`.
- If the page reloads or a server round-trip occurs, the application should restore the previously active tab when possible.
- A separate Variables CRUD widget appears at the bottom of the workbench below the tabbed area.
- The Variables CRUD widget is not part of the tab set.
- The Variables CRUD widget remains visible below the tabs regardless of the active tab.
- The tabbed workbench area and the Variables CRUD widget are separate cards or widget panels for Card Selector purposes.

### Status Tab

- The Status tab appears in the `tab01` position.
- The visible tab label is `Status`.
- At this stage, the Status tab contains a bundle asset-count list.
- The list shows every bundle.
- Each row shows:
  - Bundle name
  - Count of assets assigned to that bundle
- Asset counts are calculated from the `assets.bundle_id` relationship.
- Bundles with no assets should still appear with count `0`.
- The list is read-only in this first implementation.
- No create, edit, delete, import, export, or form controls are included in the Status tab at this stage.
- The list should use compact Bootstrap 5 table or list styling suitable for the workbench.
- The Status tab must follow the same tab-persistence rules as the other workbench tabs.

Tab registry:

| Tab ID | Visible Label | Initial Content |
| --- | --- | --- |
| `tab01` | `Status` | Bundle asset-count list |
| `tab02` | `Assets` | Asset form/list widget |
| `tab03` | `Bundles` | Bundle form/list widget |
| `tab04` | `Areas` | Area form/list widget |
| `tab05` | `Holdings` | Holdings form/list widget |
| `tab06` | `History` | History list widget |
| `tab07` | `Import` | Transaction import widget |
| `tab08` | `tab08` | `info08` placeholder |
| `tab09` | `tab09` | `info09` placeholder |
| `tab10` | `tab10` | `info10` placeholder |

## Scope

Included:

- Front page layout specification
- Header line
- Full-width top area
- Top-area left/right alignment
- 3-column sidebar
- Sidebar widgets: Card Selector, Time, Git Status, and System Status
- 9-column workbench
- Bottom-of-workbench Variables CRUD widget
- Card Selector in the `side01` position
- Table Size in the `top08` position
- Instance Info in the `top09` position
- Status tab in the `tab01` position
- 10-tab workbench with stable internal ids and visible labels
- Persisted active tab state during widget interactions

Not included:

- Real data
- Business logic
- User-specific state

## UI Notes

- Use Bootstrap 5 grid classes for the 12-column structure.
- Use Bootstrap cards for placeholder boxes.
- Use the standard utility card header style for all sidebar cards, `top08`, and `top09`.
- Use Values01 naming, layout, and Bootstrap 5 conventions.
- Use Bootstrap 5 grid or flex utilities to keep `top01` left and `top08`/`top09` right on desktop.
- Use Bootstrap tabs for the workbench.
- Use the visible tab labels from the tab registry in the UI.
- Preserve the active Bootstrap tab state during widget interactions.
- Avoid large vertical padding between the header and the top area.
- Prefer compact Bootstrap spacing such as `pt-2` or `pt-3` for the main content top padding.
- Keep the layout functional and quiet, suitable for a working application.

## Edge Cases

- On small screens, the sidebar and workbench may stack vertically.
- Tab labels should remain readable and not overflow awkwardly.
- Placeholder cards should make their region names visible.
- Widgets should not introduce unrelated dependencies.
- Widgets should not break the 12-column top area or 3-column sidebar layout.
- Card Selector registration should treat the tabbed workbench and Variables widget as distinct selectable cards.
