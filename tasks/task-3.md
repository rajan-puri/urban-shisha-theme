# Task 3 — Dynamic shell interactions and WooCommerce cart refresh

## Goal

Integrate the dynamic PHP header/footer with shared JavaScript and WooCommerce cart-count refresh. Preserve menu/account/age behaviors and remove preview catalogue/config dependencies from the PHP shell.

## Context

Depends on Task 2. Read AGENTS.md, task-2.md, its resulting PHP helpers/templates and the active Forge skill/rules. You are Worker; Manager owns Git, setup and verification. Only change listed files; use file tools, not shell commands. The approved static HTML preview must still work with its existing scripts.

## Files to touch

- `functions.php` — add a WooCommerce shell module include only.
- `inc/woocommerce.php` — new module for real cart-count fragments and safe absent-Woo behavior.
- `inc/assets.php` — enqueue WC cart fragments with dependencies where Woo is available, inject saved shell context, remove preview catalogue/config requirements from PHP enqueue only.
- `assets/js/theme-shell.js` — shared shell interactions supporting optional dynamic content.

## Constraints

- No feature code directly in functions.php. No edits to static HTML, catalogue/config/vendor files, SCF plugin or other paths. No commits, DB writes or activation. No permission bypass.
- Preserve account disclosure coordination via existing navigation.js and existing IDs/classes.
- Remove art-N/photo hydration from PHP shell script; saved images are rendered by PHP. Do not load `catalog.js` or `config.js` on PHP shell pages merely for navigation photos/social URLs.
- Mega and mobile behaviors must work independently: an empty/absent mega menu must not disable mobile toggle or sticky header. Click/tap, mouse hover, ArrowDown, Escape, outside click and focus-out must work without JS errors. Dynamic menus can be empty.
- Search must use real WordPress product-search parameters `s` and `post_type=product`, not the static preview's `search` parameter. Provide a usable accessible search interaction (native GET search form or controlled search prompt/input); do not silently navigate with an empty search value.
- Cart fragment selector `span.cart-count` must update using the real WC cart quantity. Enqueue needed native WC scripts explicitly. No demo/localStorage commerce; do not implement a new cart/checkout.
- Render contacts/social as normal PHP anchors; don't use blank static URBAN_STORE config. Footer mobile accordion handles any assigned columns; preserve desktop visibility.
- Newsletter stays honest: no fake subscribed state; clear unconnected message when submitted. Age gate preserves 18+ enforcement and storage failure handling; content comes from PHP settings. Policy page exclusion continues.
- Preserve GSAP loading and approved visual identity. No unsolicited animation redesign.

## Acceptance criteria

- [ ] PHP shell has no catalogue/config script dependencies; saved mega images remain visible.
- [ ] WooCommerce fragment response updates cart count to real quantity; disabled/absent Woo is graceful.
- [ ] Product search sends a real nonempty query with s/post_type=product.
- [ ] Desktop/mobile navigation, account disclosure, footer accordion and age gate work at 1440/1024/768/390/320 widths, including empty mega-card configuration.
- [ ] Newsletter never claims submission to an unconfigured provider.
- [ ] No PHP warnings, browser errors, broken assets or horizontal overflow; lint/build and relevant PHP checks pass.
- [ ] Only allowlisted files changed; theme remains inactive and other pages/catalogue unchanged.

## Feedback

No attempts yet. Manager records independent verification here.
