# Goal
Convert Cart, Checkout and My Account to approved HTML layouts with native WooCommerce sessions, checkout/customer authentication/orders/addresses and persistent wishlist.

# Context
Run after Home9 and Shop/Product10. User authorizes full conversion through agy, same design, no commits. Sources cart.html/checkout.html/account.html and associated CSS/JS/design.md. Preserve complete header/footer/drawers and other new templates. No preview localStorage cart, fake account demo/orders or simulated checkout. Imported catalogue stock/status unchanged. Local store Coming Soon remains. Gateways/shipping are not fully configured: retain honest native Woo unavailable messages, never fake successful payments/delivery estimates.

# Files to touch
- functions.php (modular require only)
- inc/assets.php
- inc/customer-pages.php (new)
- page.php (Woo content routing only)
- template-parts/checkout-header.php (new approved compact header)
- template-parts/checkout-footer.php (new approved compact footer)
- woocommerce/cart/cart.php (new)
- woocommerce/cart/cart-empty.php (new)
- woocommerce/cart/cart-totals.php (new)
- woocommerce/cart/proceed-to-checkout-button.php (new)
- woocommerce/checkout/form-checkout.php (new)
- woocommerce/checkout/form-billing.php (new)
- woocommerce/checkout/form-shipping.php (new)
- woocommerce/checkout/review-order.php (new)
- woocommerce/checkout/payment.php (new)
- woocommerce/checkout/thankyou.php (new)
- woocommerce/myaccount/my-account.php (new)
- woocommerce/myaccount/form-login.php (new)
- woocommerce/myaccount/navigation.php (new)
- woocommerce/myaccount/dashboard.php (new)
- woocommerce/myaccount/orders.php (new)
- woocommerce/myaccount/my-address.php (new)
- woocommerce/myaccount/form-edit-address.php (new)
- woocommerce/myaccount/form-edit-account.php (new)
- template-parts/account-wishlist.php (new)
- assets/customer-wordpress.css (new integration only)
- assets/js/customer-wordpress.js (new guarded interactions)
- scripts/seed-customer.php (new if needed classic Woo page wiring only)
- tasks/task-11.md (Feedback only)

# Constraints
- file view/edit tools only; no commands/commits/push, no unrelated changes.
- Match approved responsive layouts/classes/spacing, large cart product rows/quantity/save/remove, summary/coupon/empty state; Checkout compact logo/account header, two-column address form/order summary, native field errors, payment options and consent; Account editorial auth panel/forms, actual dashboard/sidebar/metrics/orders/addresses/details/wishlist.
- Native Woo forms, validation/nonces/hooks, account login/register (respect or document registration setting), order endpoints including view-order/lost-password/reset-password, session coupon/shipping/taxes/totals; never duplicate pricing/order models.
- Keep compatibility native WC cart/checkout JS; don't bind preview scripts that double-submit or replace backend state. Cart quantity obeys stock/max/sold-individually; server validates actual quantities and coupons. Native checkout field selectors/order-review/payment events preserved. Checkout no fake delivery/payment marks; unconfigured gateway remains truthful.
- Wishlist endpoint/display uses core saved real IDs and shared drawer JS; actual WC customer addresses. Forms must never auto-complete/save fake customer profiles.
- Guard JS on missing fields/reduced-motion. Editable interface copy can be translatable; main branding/business comes existing global SCF. Actual orders/reviews only.
- Keep concise modular functions, preserve design source HTML.

# Acceptance criteria
- [ ] Cart/Checkout/Account match original desktop/mobile visuals and all real native WC flows are wired.
- [ ] Cart update/remove/coupon and checkout validation use server calculations, quantities/stock checks.
- [ ] Real login/register/logout/password/address/account/order/wishlist state; no preview/demo data.
- [ ] No JS/PHP errors, overflow, duplicate event handlers, or header/footer regressions.

# Feedback
Pending implementation after task10.

Manager note: inspect actual Cart8/Checkout9/MyAccount10 content. These must use classic WC shortcodes for PHP overrides to execute; existing blocks bypass them. If conversion needed write idempotent scripts/seed-customer.php (now allowlisted) for manager execution, preserves custom content and sets only known placeholder/block defaults. Enable MyAccount registration only through explicit documented seed if currently disabled. Prefer current installed WC template hooks/field names over assumptions.
