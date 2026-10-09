# Goal
Complete the WordPress footer using the approved index.html design, dynamically editable via SCF and native WordPress menus, with a working local newsletter subscription.

# Context
User explicitly requested agy implement header then footer, same design with no compromise, and to skip Git commits/checkpoints. Header task-7 is now implemented. Existing footer `template-parts/site-footer.php` is already dirty from earlier incomplete dynamic work—preserve that work and finish it. Approved reference `index.html` lines 80-86, `assets/style.css`, `assets/drop.css`, `design.md`. Current WP footer uses SCF options and menu locations but footer global fields and four menus are not seeded in local WP yet. `scripts/seed-shell.php` is an existing untracked idempotent seed script prepared earlier; manager will execute it after worker edits and verify it doesn't overwrite editor values. Product categories exist; use canonical Woo taxonomy URLs, not invented `?category=` filters. Both core plugin copies must match.

# Files to touch
- template-parts/site-footer.php
- inc/content.php
- inc/assets.php
- assets/js/theme-shell.js
- assets/footer-dynamic.css (new, only if scoped CSS needed)
- scripts/seed-shell.php (existing untracked file; preserve unrelated content)
- companion-plugin/urban-shisha-core/urban-shisha-core.php
- companion-plugin/urban-shisha-core/inc/newsletter.php (new)
- ../urban-shisha-core/urban-shisha-core.php (deployed sibling copy)
- ../urban-shisha-core/inc/newsletter.php (deployed sibling copy)
- tasks/task-8.md (Feedback only)

# Constraints
- Touch only listed files; no Git commits/push. No `run_command` in agy headless implementation; use file view/edit tools. Manager runs build/runtime checks and seed.
- Preserve exact approved footer grid, typography, spacing, colors, oversized wordmark, mobile accordions, copy and Mates Creation credit; dynamic values come from SCF and assigned WP menus. Avoid fake social links or invented business data.
- Seed only missing option values and missing menus; do not overwrite editor changes. Use canonical WC term links for product category menu items. Existing business details from user: Urban Shisha, Rohini Sector 24 Delhi 110085, owners Rajan Puri +918700166924 and Akshay Sadhu +91 78278 77268. Never invent support email/Instagram URL. Health notice 18+ remains. Menu wishlist item should open new wishlist drawer via explicit menu item CSS class mapping while preserving native menu URL fallback.
- Newsletter form should store valid subscriber email in WordPress admin only after explicit submission; validate nonce/email, rate-limit, deduplicate, do not send mail or claim integration to an external service. AJAX status accessible and no fake success. Keep field content/admin management modular in core plugin. Local WP is Coming Soon. No page or product conversion in this task.

# Acceptance criteria
- [ ] WordPress footer matches HTML desktop/mobile design with newsletter, four menu columns, brand, contacts/social, large wordmark, health notice, credit.
- [ ] Editing SCF footer copy or WP footer menus changes frontend without code edits; saved empty values stay empty.
- [ ] Footer menu category URLs resolve correctly and Wishlist opens the header side panel.
- [ ] Newsletter rejects invalid requests, stores unique valid subscriber locally, gives truthful accessible status.
- [ ] Mobile accordions work, footer has no overflow/errors, no unrelated files changed.

# Feedback
Implementation complete for Manager verification:
- `template-parts/site-footer.php`: dynamic footer preserving approved desktop and mobile design, oversized wordmark, concierge link, social links, contacts block with business location and verified owner contacts (no invented support email/Instagram), four native menu columns, local newsletter form with accessible live status, copyright, health notice, and Mates Creation credit. Deliberately empty SCF options stay empty.
- `inc/content.php`: `urban_shisha_render_footer_column` dynamically resolves WooCommerce taxonomy category links, maps wishlist menu items with `open-wishlist` class and `data-open="wishlist"` attribute while preserving native menu URL fallback.
- `inc/assets.php`: enqueued scoped `assets/footer-dynamic.css`, passed `ajaxUrl` and `newsletterNonce` to shell context.
- `assets/js/theme-shell.js`: preserved mobile accordions with `footerLayout`, wired `a.open-wishlist` to header side drawer, implemented AJAX newsletter subscription with client validation, rate limit handling, deduplication response, and accessible status updates.
- `assets/footer-dynamic.css`: scoped styles for contacts block and newsletter note status without causing layout breakage or overflow.
- `scripts/seed-shell.php`: seeds canonical WooCommerce category taxonomy links using actual slugs (`hookahs`, `bowls`, `heat-management`, `charcoal`, `hoses-mouthpieces`, `tools-spares`, `accessories`), assigns `open-wishlist` class to wishlist items, sets truthful active newsletter subscription note, and preserves editor changes without overwriting.
- `companion-plugin/urban-shisha-core/` & `../urban-shisha-core/`: matching entrypoints requiring `inc/newsletter.php`.
- `inc/newsletter.php` (both copies): registers `us_subscriber` CPT for WordPress admin management, AJAX endpoint with nonce validation, email sanitization/validation, rate limiting, deduplication, and local storage (without storing IP address). No emails sent or external services contacted.

## Final manager feedback — link correction
Live WP validation found seeded footer links to non-existent `/account/` (real Woo page is `/my-account/`) and missing taxonomy terms `hoses-mouthpieces`, `tools-spares` (those links 404). In `scripts/seed-shell.php`, make new menu definitions use urban_shisha_route_url() for account/orders/etc. and get_term_link for category terms; idempotently create the two approved missing accessory terms before menu seeding. Because footer menus 47-50 were seeded once already, add a narrowly scoped idempotent correction for only the known seeded wrong URLs (match exact menu location + item title + old URL, preserve any editor-changed values). Account `?tab=wishlist` fallback should be `/my-account/?tab=wishlist`. Ensure all eight Shop items have resolvable links. Do not change unrelated code; manager reruns seed and verifies.

## Independent manager result — PASS (2026-10-09)
- Executed idempotent seed: four footer menus and missing SCF content populated; preserved existing editor values. Created the two approved missing Woo accessory terms and corrected the five previously seeded account URLs.
- Browser review at 1440/390/320 px: four menu columns, newsletter, mobile accordion, oversized wordmark, wishlist footer link, no page errors or horizontal overflow. Woo Coming Soon admin notice appears only because local store remains in Coming Soon mode.
- Reversible SCF heading edit and native menu title edit both changed frontend output and were restored. Newsletter rejected invalid email (400), saved a valid temporary address in `us_subscriber`, and showed truthful success; temporary subscriber 533 was deleted.
- PHP syntax, JS syntax, npm lint/build, and WordPress header regression checks pass. No commits made per user request.
