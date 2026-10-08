# Goal
Complete the existing header conversion using WordPress menus, SCF global settings and real WooCommerce state.

# Context
Owner authorises direct Codex work; agy only when explicitly asked. Preserve approved header typography/spacing, desktop/mobile image mega-menu and account dropdown. Prior uncommitted content/header helpers are the baseline for this task; unrelated footer, age-gate, style.css and seed-shell changes must remain untouched. Existing products remain drafts. Coming Soon remains enabled.

# Files to touch
- functions.php (module include only)
- inc/content.php
- inc/header.php
- inc/assets.php
- template-parts/site-header.php
- template-parts/header-search.php
- assets/js/theme-shell.js
- assets/navigation.css and generated assets/site.css / dist (only build output)
- scripts/seed-header.php
- scripts/verify-header.php
- scripts/check-header.mjs
- HEADER-DYNAMIC.md
- tasks/task-6-header-verification.json
- tasks/task-6-dynamic-header.md
- tasks/task-6-header-baseline.json
- companion-plugin/urban-shisha-core/fields/group-us-global.json
- sibling wp-content/plugins/urban-shisha-core/fields/group-us-global.json
- Local WordPress options, primary/header shortcut menu assignments/items, category terms needed for existing preview categories, and header media only.
- Temporary private browser auth and review artifacts outside web root.

# Constraints
No redesign, page publication, product/price/stock changes, footer seeding, store-mode changes, agy, push or merge. Seed only absent header content; intentional empty settings must remain empty. Menus use native WP targets and attributes; category destinations resolve from selected taxonomy terms. Reuse approved transparent local cutouts. No catalogue/config preview dependencies for header. Normal UI labels may remain translated code. Avoid inventing announcement delivery/promotional promises.

# Acceptance criteria
- [x] Admin can edit logo, announcement, primary menus and mega-menu cards/text/links.
- [x] Taxonomy cards resolve canonical category URLs and share desktop/mobile data.
- [x] Native WP menu hierarchy/targets/current states work with keyboard, hover and touch.
- [x] Mobile toggle works even with no Shop trigger or assigned primary menu.
- [x] Product search submits native s + post_type=product; no old preview search query.
- [x] Account login/logout/endpoints reflect real WP/WC state; cart badge refreshes via WooCommerce.
- [x] Header renders correctly across desktop/tablet/mobile with no overflow/JS errors.
- [x] Editing saved header data updates rendering; seed rerun preserves editor changes.
- [x] PHP/JS/static checks, real WP rendering and browser interactions pass.

# Feedback
PASS: Real WP rendering/editor updates, native taxonomy links, editable logo, intentional empty settings, guest/logged-in account/endpoints, Woo cart fragments and real AJAX, product-search parameters and menu hierarchies/targets verified. Browser checks pass at 1440/1024/768/390/320px, including three-level menus and mobile without a mega trigger, with zero JS runtime errors. Fixed inline category photo containers and detached-SVG outside-click handling found during preview. Build/lint and changed PHP syntax pass. Existing draft products and Coming Soon preserved. Reports: tasks/task-6-header-verification.json; screenshots/private fixtures outside the web root. Prior unrelated footer/age-gate/style/seed-shell work is unstaged and preserved.
