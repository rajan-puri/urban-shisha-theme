# Task 4 — Hide Homepage content admin submenu

## Goal

Remove the Homepage content submenu under Urban Shisha in WP Admin for now.

## Context

User wants homepage fields managed elsewhere later. Preserve all existing fields and saved data. You are agy WORKER; Codex MANAGER performs verification and Git. Read AGENTS.md and installed Forge rules if needed. This is a tiny change: do not audit unrelated pages or rewrite code. Use file tools only, no shell commands or recursive delegation.

## Files to touch

- `/Users/rajan/Local Sites/urban-shisha/app/public/wp-content/plugins/urban-shisha-core/inc/cms.php` — remove ONLY the `acf_add_options_sub_page` registration for `urban-shisha-home`.

## Constraints

- No other changes. Keep the Urban Shisha global settings options page and all field-group registration intact.
- Do not delete JSON files, options/database values, or migrate homepage fields yet.
- Do not edit this task file, theme files, Git, dependencies or settings.
- No permission bypass. Manager has a snapshot of this sibling-plugin file at `/tmp/urban-home-menu-cms.before.php` and SHA256 `9264cacbe2a363dcc567ecf467341f908e03cbf17d1ca14736e25876826b44b0`; the theme Git branch/checkpoint records scope. Plugin is outside the theme repository; it must be verified separately.

## Acceptance criteria

- [ ] `acf_get_options_page('urban-shisha-home')` is false/unregistered.
- [ ] Global `urban-shisha-settings` options page still registered.
- [ ] All three original SCF field groups remain registered and their JSON unchanged.
- [ ] Diff contains only removal of the 7-line homepage submenu registration; PHP syntax passes.
- [ ] Other files and existing data remain unchanged.

## Feedback

No attempts yet.
