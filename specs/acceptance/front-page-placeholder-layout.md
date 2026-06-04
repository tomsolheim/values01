# Acceptance: Front Page Placeholder Layout

## Criteria

- The front page shows a header line.
- The top area spans the full 12-column width.
- The top area shows placeholder cards named `top01`, `top08`, and `top09`.
- The main content area has a sidebar that is 3 grid columns wide on desktop.
- The main content area has a workbench that is 9 grid columns wide on desktop.
- The sidebar shows placeholder cards named `side01` and `side02`.
- The workbench shows 10 tabs named `tab01` through `tab10`.
- Each tab shows matching placeholder content from `info01` through `info10`.
- The layout remains usable on small screens.

## Suggested Tests

- A feature test confirms the front page contains `top01`, `top08`, and `top09`.
- A feature test confirms the front page contains `side01` and `side02`.
- A feature test confirms the front page contains tab labels `tab01` through `tab10`.
- A browser or Livewire test confirms each tab can reveal its matching `info` placeholder.
