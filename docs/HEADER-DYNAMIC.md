# Dynamic header

The approved header is connected to WordPress and WooCommerce. Its design, desktop nav baseline (16px, weight 700, gap 23px), mobile layout and transparent imagery are preserved.

## Edit in Admin

- **Urban Shisha → Global settings → Branding:** brand name and Media Library logo. An empty logo uses the editable brand-name wordmark.
- **Announcement:** enable/disable, text and optional link. Empty announcement content hides the strip. Seeded text is brand/18+ copy, with no delivery or offer promises.
- **Navigation & mega menu:** eyebrow, heading, category cards, shop link, footer note and mobile category heading. Reorder/remove cards through the repeater. Each card selects a WooCommerce category and image, with an optional label override. Category selections resolve their canonical URLs; a custom destination is used only without a category. Desktop/mobile share the same saved cards.
- **Appearance → Menus:** assign Main navigation and Header: Mega menu shortcuts. Add native pages/categories/custom links, reorder them, change labels/targets or add nested items. Enable CSS Classes in Screen Options and add `urban-mega-trigger` to the top-level item that should open the image menu. Its normal link still navigates to the saved destination; the adjacent chevron opens the disclosure. Removing the trigger does not break the mobile menu.
- **Wishlist destination:** leave empty until a real wishlist integration/page exists. Setting a destination exposes the header icon and account shortcut. The header does not pretend a wishlist backend already exists.

The initial six cards use the actual imported categories Hookahs, Bowls, Heat Management, Charcoal, Accessories and Tongs, with approved transparent cutouts. The main menu initially uses Shop, Hookahs, Accessories, Bulk Orders and About us. No pages/products were published by this task. About/Bulk Orders remain the previously created draft placeholders; native page menu links follow the pages when they are published. Original primary/footer content can be revised independently in Admin.

## Runtime behavior

- Product search opens an accessible dialog and submits native WordPress `s` plus `post_type=product`. It searches published WooCommerce products; the imported drafts are not public search results.
- Account dropdown uses real WordPress login state and WooCommerce account/orders/address/details/logout URLs. The account icon stays on all shared pages.
- Bag count reads the real WooCommerce cart and refreshes through `woocommerce_add_to_cart_fragments`. `wc-cart-fragments` is explicitly enqueued because the header badge appears throughout the site. No localStorage cart or preview product catalogue is used.
- Desktop menus support pointer hover, click, keyboard disclosures and Escape. Mobile navigation works independently of a Shop/mega trigger. Outside clicks use the original event path so replacing the toggle SVG cannot immediately close the menu.
- Optional content stays hidden when intentionally empty. Normal interface labels are translatable code; no arbitrary layout controls are exposed.

## Source and migration

`inc/header.php` owns header menu/card/cart helpers. `inc/content.php` retains shared branding/options/footer helpers. `template-parts/site-header.php` and `header-search.php` own markup. `assets/js/theme-shell.js` controls shared disclosures/dialogs, with the existing `navigation.js` continuing to own account behavior. Header integration CSS is in `assets/navigation.css`; `assets/site.css` and matching dist assets are generated.

Header-only seed: `wp eval-file scripts/seed-header.php`. It imports approved cutouts and fills absent header options/unassigned menus without overwriting saved empty values or editor content. Do not run the old unfinished `scripts/seed-shell.php` for this task. Footer seeding/conversion is separate.

SCF additions live in the deployed sibling Urban Shisha Core `fields/group-us-global.json` and its versioned `companion-plugin/urban-shisha-core/fields/` copy. Keep those copies in sync. Theme functions.php only gained a module include.

Native APIs follow [WordPress menu items](https://developer.wordpress.org/reference/functions/wp_get_nav_menu_items/) and [WooCommerce cart-fragment guidance](https://developer.woocommerce.com/2023/06/16/best-practices-for-the-use-of-the-cart-fragments-api/).

## Verification

```sh
npm run lint
npm run build
wp eval-file scripts/verify-header.php /tmp/urban-header-review
URBAN_PLAYWRIGHT_MODULE=/path/to/playwright/index.mjs node scripts/check-header.mjs /tmp/urban-header-review
```

PHP verification checks saved-content/menu edits, guest/logged-in account states, native search markup, canonical category links, cart fragment count, hierarchy and safe targets. It restores temporary edits and removes its own temporary menu. Browser verification covers 1440/1024/768/390/320px, hover/touch/keyboard disclosure, search submission, real cart AJAX, account visibility on WooCommerce system pages, three-level menus and mobile without a mega trigger. Private temporary auth stays outside the web root and is never committed.

**Coming Soon remains enabled.** Authenticated browser checks run against the actual site. Guest interactions use an actual PHP-rendered theme snapshot in the browser, preserving store visibility. The guest harness avoids re-registering WooCommerce's unrelated attribution custom element in the reused browser realm; deployed scripts are unchanged. This task does not release the store or convert the approved homepage/shop/product designs.
