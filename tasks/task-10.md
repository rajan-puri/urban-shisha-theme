# Goal
Convert Shop and single Product pages to the exact approved HTML layouts with real WooCommerce queries, filters, galleries and buying forms.

# Context
After task9 Home, continue user-authorized completion of all remaining theme pages. agy implements, manager verifies; no commits. Read shop.html/product.html and relevant assets/shop.css/product.css, assets/js/shop.js/product.js for exact original layout. Shared WP header/footer/drawers are implemented. Task9 introduces inc/commerce.php/product-card.php: reuse them, preserve current work. Woo has107 imported draft products (91simple+16variable), actual galleries/prices/spec fields. Do not publish/edit existing prices/stock or invent ratings. Admin-only draft preview should display catalogue for design review without public draft leaks. Product content excludes source FAQ via completed importer. Page style must match original, no default Woo layout substitution.

# Files to touch
- functions.php (modular require only)
- inc/assets.php
- inc/commerce.php (existing shared helper)
- inc/shop.php (new)
- inc/product.php (new)
- template-parts/product-card.php
- woocommerce.php (new fallback wrapper)
- woocommerce/archive-product.php (new)
- woocommerce/single-product.php (new)
- woocommerce/content-single-product.php (new)
- woocommerce/single-product/add-to-cart/simple.php (new)
- woocommerce/single-product/add-to-cart/variable.php (new)
- woocommerce/single-product/add-to-cart/variation-add-to-cart-button.php (new)
- assets/js/shop-wordpress.js (new)
- assets/js/product-wordpress.js (new)
- assets/commerce-pages.css (new scoped integration only)
- companion-plugin/urban-shisha-core/fields/group-us-shop.json (new)
- /Users/rajan/Local Sites/urban-shisha/app/public/wp-content/plugins/urban-shisha-core/fields/group-us-shop.json (matching deployed copy)
- scripts/seed-shop.php (new idempotent approved editorial content seed)
- tasks/task-10.md (Feedback only)

# Constraints
- Implement with file view/edit tools, no run_command/commits/push, touch only listed files.
- Preserve ALL approved Shop layout: editorial hero/poster, category tabs, sidebar categories/price/brands/stock/sale filters, sorting/result count/chips, grid/list toggle, pagination, mobile filter drawer/sticky actions, empty state.
- Queries must use WC main query or WC APIs. Filter URL state survives reload/back/pagination. Validate/cap inputs, escape output. Term/card names/brands/prices/counts read from real Woo taxonomy/catalog. SCF Shop editorial fields editable, no runtime HTML parsing. Admin-preview drafts supported only for authorized edit_products users; public query publish only. Do not globally expose drafts or cache admin results publicly.
- Preserve Product exact gallery/thumbnail/zoom, badges, price/rating (only actual reviews), stock, variant selection, quantities, tabs/accordions, included/specification content, recommendations, sticky mobile buy bar. Real WC simple/variable form data and validation; use WC variation JS. Out-ofstock unavailable products cannot falsely add. Wishlist shares numeric IDs/header drawer. Show actual source long-description/spec data; no fake delivery promises or product reviews.
- Product/card images prefer us_product_showcase_image alpha asset; original Woo gallery unchanged. Backdrops behind images, no blend tinting/white fill. Real product URLs; draft preview URL only for editor.
- Reuse local GSAP, guard missing elements, respect reduced motion. No fake UrbanCatalog or localStorage cart.
- Woo-compatible hooks/notices/forms/nonces, cart fragments, no copying stale preview checkout logic. Shared card styles and all original spacing remain.

# Acceptance criteria
- [ ] Shop visually matches original desktop/mobile; all filters/sort/pagination and empty state use real data.
- [ ] Product visually matches original; gallery/zoom/variations/stock/quantity/wishlist/mobile buy and real Woo forms work.
- [ ] Editorial copy editable SCF, product name/price/image updates automatically; no preview data/ratings/promises.
- [ ] Public draft visibility protected; admin can review imported draft catalogue/product pages.
- [ ] No errors/overflow or header/footer regression; allowlist respected.

# Feedback
Pending implementation after Home task9.

Pre-implementation manager notes: installed WC template loader tries root woocommerce.php BEFORE archive/single overrides. If creating woocommerce.php it must dispatch to exact new archive/single templates, not generic woocommerce_content() that bypasses approved design. Read installed native WC templates for current hooks/variation data. Shared product helpers must guard direct draft access, use actual Woo images for unknown products, draft get_preview_post_link for editors, truthful price empty. Product cards expose numeric data-price (do not parse formatted sale price strings for filtering). No public draft leak even relationship fields/direct product helpers.

Worker attempt1 blocked before implementation: headless read_file permission auto-denied while consulting installed Woo templates. Manager is granting scoped exact-file read permissions for project files/native WC templates and exact deployed shop-schema write. No feature changes yet. This is tooling blockage, not acceptance PASS. Re-run with file tools only.

User pause 9 Oct: "abhi work ko yhi roko". All active Home/Shop agy workers terminated. Partial changes retained, no commits. Home isolated output at /tmp/urban-shisha-home-worker NOT merged. Resume only on user instruction; inspect current status and incomplete files before continuing.

9 Oct — direct Codex completion requested by user, changes first followed by one consolidated final verification phase. No agy, commit or push.
- Retained category/brand archive scope across AJAX sorting, reset and navigation; valid numbered pagination survives refresh, with out-of-range requests clamped.
- Real featured priority, newest sorting, title/content/SKU/brand search, decimal-safe validated ranges and Woo parent/variation price lookup filtering.
- Saved nineteen original Shop editorial fields through SCF, preserved explicit empty edits, reused Media assets and verified migration preserves saved metadata/media count.
- Corrected native Woo add-to-cart fragment response handling, header/drawer refresh, request races, accessible loading/error/retry state and mobile close/focus handling.
- Final checks passed: PHP/JS syntax, lint/build, existing server Shop catalogue checks (107 drafts, 24/page, five pages), pagination reload, filters/search/reset, price range, newest, archive contexts, Back navigation, wishlist add/remove restoration, SKU/featured temporary fixture, actual cart add/fragments/drawer/remove, 1440/390/320 mobile and preserved Home builder.
- Current Local PHP runtime is 8.5.3; temporary WP CLI wrapper updated to match. Final browser brand test used real /brand/ archives. Completed remaining checks without repeating the already-passed checks.
- Temporary hidden test product was deleted and cart/wishlist restored. Existing drafts, stock and Coming Soon were preserved.
