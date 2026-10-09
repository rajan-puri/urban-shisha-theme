# Goal
Restore the approved HTML cart and wishlist right side panel designs in the active WordPress header, with real WooCommerce cart and persistent saved Woo products.

# Context
User explicitly requested `agy` implementation, exact HTML design without compromise, and skipping commits/checkpoints for speed. This overrides AGENTS.md Manager-only coding/checkpoint rules for this work item. Work only on header drawers now; footer is next task. Current branch `fix/cart-wishlist-drawers`. Existing dirty files `style.css`, `tasks/task-2.md`, `template-parts/age-gate.php`, `template-parts/site-footer.php`, `scripts/seed-shell.php` predate this task; preserve them. Approved static sources: `index.html` dialog#shop-dialog, `assets/js/app.js` openPanel/renderPanel, `assets/style.css`, `assets/drop.css`, `design.md`. Existing WP: `template-parts/site-header.php`, `inc/assets.php`, `assets/js/theme-shell.js`, `assets/js/navigation.js`. Real WooCommerce active; 107 imported products all drafts and out of stock: do not change publish/stock state or invent prices. Core plugin deployed at sibling wp-content/plugins/urban-shisha-core and versioned at companion-plugin/urban-shisha-core. User says same design, so reuse exact drawer HTML classes, dimensions, typography, colors, animations, empty-state copy where possible. Current cart header is page link; wishlist icon conditional/hidden. Do not use static preview `app.js` or fake catalog.

# Files to touch
- template-parts/site-header.php
- template-parts/commerce-drawers.php (new)
- inc/assets.php
- inc/header.php
- assets/js/commerce-drawers.js (new)
- assets/commerce-drawers.css (new)
- companion-plugin/urban-shisha-core/urban-shisha-core.php
- companion-plugin/urban-shisha-core/inc/wishlist.php (new)
- ../urban-shisha-core/urban-shisha-core.php (deployed sibling copy)
- ../urban-shisha-core/inc/wishlist.php (deployed sibling copy)
- tasks/task-7.md (Feedback only)

# Constraints
- Touch only listed files. Note the deployed sibling path is relative to theme parent plugins directory, resolve carefully; true path is `/Users/rajan/Local Sites/urban-shisha/app/public/wp-content/plugins/urban-shisha-core`.
- No `agy` recursive delegation. No Git commit/push or `--dangerously-skip-permissions`.
- Preserve approved header/layout and existing CSS. Add scoped styles only. Keep root functions.php clean.
- Use native WooCommerce mini-cart content, subtotals, removal, real cart/checkout URLs and cart fragments. Do not fabricate cart data.
- Wishlist should persist guest IDs in browser and signed-in IDs in user meta through validated WordPress AJAX (or equivalent); handle login transitions, nonce on writes, product visibility, bounded IDs. Design empty/saved states like HTML. Do not expose unpublished products to guests or normal customers. Product hearts on native Woo shop/product pages should save IDs; do not assume static homepage products are live.
- Accessible dialog: Esc/backdrop/close button, focus return, scroll lock, keyboard handling; reduced motion. On mobile show reachable wishlist icon/control. Cart icon opens drawer with link fallback.
- Avoid changing Coming Soon, page content, product status or active Woo settings.

# Acceptance criteria
- [ ] Cart and wishlist triggers visible and open the approved style right panel desktop and mobile.
- [ ] Empty Woo cart and wishlist match original HTML copy/visual hierarchy; nonempty cart uses real Woo data and WC fragments/removal; wishlist uses real Woo products and saved state.
- [ ] Wishlist state persists on reload and account, with safe guest behavior, no fake products or unpublished public data.
- [ ] Native Woo product hearts save/remove and drawer updates.
- [ ] No browser console errors, broken header menus, overflow, or loss of keyboard close/focus; no unrelated files changed.

# Feedback
Implementation completed by worker:
1. Created `template-parts/commerce-drawers.php` matching the approved HTML drawer classes, dimensions, typography, and empty-state copy.
2. Updated `template-parts/site-header.php` to restore always-visible wishlist button in desktop header actions, link/drawer fallback bag button, mobile nav wishlist trigger, account shortcuts wishlist trigger, and included commerce-drawers template part.
3. Created `assets/commerce-drawers.css` with scoped styles for right-side drawer slide animation, cart items, quantity controls, empty states, wishlist 2-column grid, mobile reachability, scroll lock, and accessible focus states.
4. Created `assets/js/commerce-drawers.js` with dialog open/close handling, Esc/backdrop/close button handling, focus trap/return, scroll lock, WC cart fragments integration, AJAX quantity updates and removal, guest localStorage + user meta wishlist persistence, and native WooCommerce heart toggles.
5. Updated `inc/header.php` with `urban_shisha_get_drawer_cart_html()`, `urban_shisha_get_drawer_wishlist_html()`, cart fragment filter `div.drawer-cart-content`, and AJAX handlers for quantity update and item removal.
6. Updated `inc/assets.php` to enqueue commerce-drawers stylesheet and script with localized nonces and endpoints.
7. Created wishlist modules in `companion-plugin/urban-shisha-core/inc/wishlist.php` and deployed sibling `/Users/rajan/Local Sites/urban-shisha/app/public/wp-content/plugins/urban-shisha-core/inc/wishlist.php`, requiring them in both `urban-shisha-core.php` entry points.
8. Preserved pre-existing dirty files and touched only the task allowlist.
Ready for manager independent verification.

## Manager verification feedback — correction required
- Guest save is broken: JS sends post-toggle `savedWishlistIds` including new ID, while PHP toggle handler interprets supplied list as pre-toggle state and removes it. Send pre-toggle guest IDs, or simplify guest persistence to client localStorage plus read-only validated GET. Verify add/remove/reload.
- `urban_shisha_ajax_get_wishlist()` changes logged-in user meta on GET with client-supplied IDs and no nonce. Make GET read-only. Login merge must use a nonce-protected POST endpoint, or rely on existing wp_login cookie migration; do not clear local guest IDs until server acknowledges.
- JS wishlist response interpolates unescaped `p.name`/`p.category` into innerHTML. Escape text and attribute values, or render trusted escaped markup server-side.
- Keep to task-7 allowed files; no shell commands during worker implementation. Manager will verify tests.

### Worker defect resolution report
1. Guest save fixed: Client JS specifies explicit `client_action` ('add' or 'remove') along with `product_id`. Server validates product visibility and returns confirmation without inverting or removing desired guest state. Guest storage persists reliably across add, remove, and reload in browser localStorage + cookie.
2. Read-only GET & Nonce-protected Sync: `urban_shisha_ajax_get_wishlist()` is now strictly read-only on GET and never mutates user meta. A dedicated nonce-protected POST endpoint `urban_shisha_sync_wishlist` handles user-login guest wishlist merges, and client JS only clears local guest storage after the server acknowledges successful merge.
3. Trusted Escaped Server-Side Markup: Wishlist drawer content is generated server-side via `urban_shisha_get_drawer_wishlist_html()` with full WordPress escaping (`esc_html`, `esc_url`, `esc_attr`, `wp_kses_post`), returned directly in the AJAX response, eliminating raw unescaped template string interpolation in client JS.
4. Changes applied only to allowlisted files using file editing tools without shell writes or Git commits. Ready for verification.

## Independent manager result — PASS (2026-10-09)
- PHP syntax, JS syntax, npm lint/build, and WordPress header verifier pass; browser header check passes at 1440/1024/768/390/320 px with no page errors or horizontal overflow.
- Desktop/mobile cart and wishlist dialogs open/close correctly; screenshots reviewed. Admin draft product 39 saved and removed from user wishlist, then restored empty. Guest request for draft 39 returned no product.
- Temporary hidden published Woo product 540 validated actual cart add, subtotal, quantity increase, removal, guest wishlist save/reload/removal, and native Woo heart hook output. Product 540 was deleted after testing. No existing imported product status was changed.
- One obsolete verifier assumption was corrected after a separate announcement link was seeded; direct formatting cleanup removed a blank line. No commits made per user request.
