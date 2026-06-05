# Acceptance: Bundle and Area Tab Widgets

## Criteria

- The application has a `bundles` table.
- The `bundles` table includes `name`, `comment`, and timestamps.
- The application has an `areas` table.
- The `areas` table includes `name`, `comment`, and timestamps.
- `name` is required for bundles.
- `name` is required for areas.
- `comment` is optional for bundles.
- `comment` is optional for areas.
- `tab03` shows the Bundle form/list widget.
- `tab04` shows the Area form/list widget.
- The Bundle widget heading includes a show/hide button for the form.
- The Area widget heading includes a show/hide button for the form.
- The Bundle list remains visible when the form is hidden.
- The Area list remains visible when the form is hidden.
- The Bundle widget show/hide button affects only the form area.
- The Area widget show/hide button affects only the form area.
- The Bundle widget can create a bundle.
- The Bundle widget can list existing bundles.
- The Bundle widget can edit an existing bundle.
- The Bundle widget can delete a bundle.
- The Bundle list follows the shared CRUD list behavior.
- The Area widget can create an area.
- The Area widget can list existing areas.
- The Area widget can edit an existing area.
- The Area widget can delete an area.
- The Area list follows the shared CRUD list behavior.
- The Bundle list is paginated.
- The Area list is paginated.
- The Bundle list has a search field.
- The Area list has a search field.
- The Asset form Bundle dropdown is populated from `bundles.name`.
- The Asset form Area dropdown is populated from `areas.name`.

## Suggested Tests

- A migration test or database assertion confirms the `bundles` table exists with the expected columns.
- A migration test or database assertion confirms the `areas` table exists with the expected columns.
- A feature or Livewire test confirms bundles can be created, listed, updated, and deleted.
- A feature or Livewire test confirms areas can be created, listed, updated, and deleted.
- A feature or Livewire test confirms the Asset widget receives bundle options from the bundles table.
- A feature or Livewire test confirms the Asset widget receives area options from the areas table.
