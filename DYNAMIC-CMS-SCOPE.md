# Urban Shisha: dynamic WordPress scope

Status: agreed requirement and implementation plan; CMS conversion is pending. The WordPress theme foundation exists and is inactive. Do not describe the static previews as finished WooCommerce pages.

## Owner requirement

All main customer-facing content must be editable through WordPress Admin. Preserve the approved design, responsive spacing, transparent product photography and GSAP motion. No Elementor or other page builder. Technical structure and decorative elements may remain in code.

## Data ownership

| Area | Admin source and required behavior |
| --- | --- |
| Products | WooCommerce products: names, descriptions, prices, sale prices, SKU, stock, variations, attributes, galleries and categories. Never duplicate prices or stock in custom fields. |
| Product imagery | WordPress Media Library and WooCommerce galleries. Allow a separate transparent showcase image when required by the approved composition; preserve alpha and put decorative shapes behind images. |
| Brands | Product brand taxonomy with editable name, logo/image and destination. Select existing brand terms for homepage cards rather than maintaining a duplicate brand list. |
| Global content | Secure Custom Fields options for logo, announcement, contact details, social links, age-gate copy, footer content and shared promotional content. Navigation uses native WordPress menus. |
| Homepage | SCF groups for every approved section: hero text/media/CTA; categories; brands; mood cards; hookah rail; large-product/four-product spotlight; kit cards; accessories; budget groups; fresh arrivals; setup builder introduction; editorial banners; guides; newsletter copy. |
| Homepage product selection | Relationships to real WooCommerce products/categories/brands, plus configurable queries for latest, featured and price ranges. Prices, availability and destinations resolve from WooCommerce at render time. |
| Section management | Show/hide and reorder approved sections using controlled fields or flexible content. Repeated cards, FAQs and editorial items use repeaters. Do not expose arbitrary CSS controls that can break the design. |
| Shop and product pages | WooCommerce queries, taxonomy filters, sorting, pagination, product variations, stock-aware purchasing and real gallery data. SCF only supplies additional editorial content not covered by WooCommerce. |
| Cart, checkout and account | Real WooCommerce cart/session, checkout validation, orders, customer login/registration, order history and addresses. Replace preview localStorage commerce. Payment gateways require their actual configuration. |
| Wishlist | Persist selected product IDs for customers, with an explicit guest behavior. SCF is not the wishlist or authentication backend. |
| About, contact and policies | Native WordPress page content and structured SCF fields where the approved layout needs them. Editable intros, images, business details, FAQs and policy text. |
| Wholesale | Editable page content, catalogue-driven enquiry items/quantities and a real server-side enquiry record/workflow. It remains separate from the retail cart. Warehouse animation is deferred as recorded in WHOLESALE-PLAN.md. |
| Contact and newsletter | Real server-side submissions or configured service integration, validation and truthful success states. Editable copy is separate from submission functionality. |

## Business information

Seed confirmed details from BUSINESS-DETAILS.md only. Urban Shisha is based in Rohini Sector 24, Delhi 110085, with no physical retail store. Both owners' contact details must be editable. Do not choose a primary WhatsApp number, invent an email, opening hours, payment terms or shipping/returns promises. Missing operational details remain unconfigured and visible as setup requirements in admin.

## Content migration and architecture

- Import approved existing copy and local assets into the database/Media Library once. Templates then read saved content; runtime parsing of static HTML is not the CMS implementation.
- Keep SCF field definitions version-controlled in PHP or local JSON, with content values stored in the database.
- Use WooCommerce APIs for product/cart/order data and native WordPress APIs for menus, pages and media.
- Keep durable business functionality, such as wholesale enquiry storage and wishlist persistence, separate from visual theme templates, preferably in a small project plugin.
- Define how empty optional sections are hidden and how required missing content is flagged in admin. Do not mask incomplete configuration with permanent hardcoded sample content.
- Keep layout, breakpoints, icon artwork, decorative shapes and GSAP choreography in code. Standard interface labels should remain translatable.

## Implementation sequence

1. Audit current fields/plugins, add SCF where needed, define global settings and section schemas.
2. Seed approved content and media, bind shared header/footer/menus/contact settings to admin data.
3. Convert the approved homepage into dynamic templates without changing its design.
4. Connect shop, product, cart, checkout and account to real WooCommerce data and flows.
5. Convert remaining content pages and implement wishlist, wholesale and form submission functionality.
6. Verify admin editing, responsive visuals, purchasing flows and GSAP behavior before activation/release.

## Acceptance

Changing a product price, stock or name once in WooCommerce must update all places showing that product. Editors must be able to change main copy, imagery, CTA links, curated products, menus and footer details without editing code. Required content and functional forms must not silently use preview data. Approved visual identity must remain intact on desktop and mobile.
