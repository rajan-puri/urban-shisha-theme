<?php
/**
 * Shared site header template part.
 */
defined( 'ABSPATH' ) || exit;
?>
<svg class="icon-library" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"><defs>
<symbol id="i-arrow" viewBox="0 0 24 24"><path d="M4 12h16m-6-6 6 6-6 6"/></symbol>
<symbol id="i-bag" viewBox="0 0 24 24"><path d="M5 7h14l1 14H4L5 7Z"/><path d="M8 8V6a4 4 0 0 1 8 0v2"/></symbol>
<symbol id="i-search" viewBox="0 0 24 24"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 5 5"/></symbol>
<symbol id="i-heart" viewBox="0 0 24 24"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8L12 21l8.8-8.6a5.5 5.5 0 0 0 0-7.8Z"/></symbol>
<symbol id="i-check" viewBox="0 0 24 24"><path d="m5 12 4 4L19 6"/></symbol>
<symbol id="i-plus" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></symbol>
<symbol id="i-close" viewBox="0 0 24 24"><path d="m5 5 14 14M5 19 19 5"/></symbol>
<symbol id="i-menu" viewBox="0 0 24 24"><path d="M3 8h18M3 16h18"/></symbol>
<symbol id="i-chevron" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></symbol>
<symbol id="i-chat" viewBox="0 0 24 24"><path d="M21 11.5a9 9 0 0 1-9 9 9 9 0 0 1-4-.9L3 21l1.4-4.8A9 9 0 1 1 21 11.5Z"/><path d="M8 11h8M8 14h5"/></symbol>
<symbol id="i-instagram" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><path d="M17.5 6.5h.01"/></symbol>
<symbol id="i-user" viewBox="0 0 24 24"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></symbol>
<symbol id="i-mail" viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></symbol>
<symbol id="i-box" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></symbol>
<symbol id="i-map-pin" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></symbol>
<symbol id="i-copy" viewBox="0 0 24 24"><rect x="8" y="8" width="12" height="12" rx="2"/><path d="M16 8V4H4v12h4"/></symbol></defs></svg>
<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'urban-shisha' ); ?></a>
<?php
$announcement_enabled = (bool) urban_shisha_get_option( 'announcement_enabled', false );
$announcement_text    = urban_shisha_get_option( 'announcement_text', '' );
$announcement_link    = urban_shisha_parse_link( urban_shisha_get_option( 'announcement_link' ) );

if ( $announcement_enabled && ( $announcement_text || $announcement_link ) ) :
?>
<aside class="announcement" aria-label="<?php esc_attr_e( 'Announcement', 'urban-shisha' ); ?>">
	<?php if ( $announcement_text ) : ?>
		<span><?php echo esc_html( $announcement_text ); ?></span>
	<?php endif; ?>
	<?php if ( $announcement_link && ! empty( $announcement_link['url'] ) ) : ?>
		<a href="<?php echo esc_url( $announcement_link['url'] ); ?>"<?php echo ! empty( $announcement_link['target'] ) ? ' target="' . esc_attr( $announcement_link['target'] ) . '" rel="noopener noreferrer"' : ''; ?>>
			<?php echo esc_html( $announcement_link['title'] ?: __( 'Learn more', 'urban-shisha' ) ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
		</a>
	<?php endif; ?>
</aside>
<?php endif; ?>

<header class="site-header" id="top"><div class="header-inner wrap">
<a href="<?php echo esc_url( urban_shisha_route_url( 'home' ) ); ?>" class="wordmark" aria-label="<?php echo esc_attr( urban_shisha_get_brand_name() ); ?> home">
	<?php urban_shisha_render_brand_logo( 'header' ); ?>
</a>
<nav class="desktop-nav" aria-label="<?php esc_attr_e( 'Main navigation', 'urban-shisha' ); ?>">
	<?php urban_shisha_render_desktop_nav(); ?>
</nav>
<div class="header-actions">
<button class="icon-button" data-open="search" aria-label="<?php esc_attr_e( 'Search products', 'urban-shisha' ); ?>"><svg><use href="#i-search"/></svg></button>
<?php $wishlist_link = urban_shisha_parse_link( urban_shisha_get_option( 'header_wishlist_link' ) ); ?>
<button type="button" class="icon-button wishlist-nav" data-open="wishlist" aria-label="<?php esc_attr_e( 'Saved products', 'urban-shisha' ); ?>"><svg><use href="#i-heart"/></svg></button>
<div class="account-entry">
	<button type="button" class="icon-button account-nav account-trigger" id="account-trigger" aria-label="<?php esc_attr_e( 'Open My Account menu', 'urban-shisha' ); ?>" aria-expanded="false" aria-controls="account-shortcuts">
		<svg aria-hidden="true"><use href="#i-user"/></svg>
	</button>
	<div class="account-menu" id="account-shortcuts" aria-labelledby="account-menu-title" hidden>
		<p class="eyebrow"><?php esc_html_e( 'YOUR SPACE.', 'urban-shisha' ); ?></p>
		<h2 id="account-menu-title"><?php esc_html_e( 'My account', 'urban-shisha' ); ?><span class="punct">.</span></h2>
		<?php if ( is_user_logged_in() ) :
			$current_user = wp_get_current_user();
			$logout_url   = function_exists( 'wc_logout_url' ) ? wc_logout_url( urban_shisha_route_url( 'home' ) ) : wp_logout_url( urban_shisha_route_url( 'home' ) );
		?>
			<p class="account-menu-intro account-user-greeting"><?php echo esc_html( sprintf( __( 'Hello, %s', 'urban-shisha' ), $current_user->display_name ) ); ?></p>
			<a class="button account-signin" href="<?php echo esc_url( urban_shisha_route_url( 'account' ) ); ?>"><?php esc_html_e( 'Account dashboard', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></a>
			<nav aria-label="<?php esc_attr_e( 'Account shortcuts', 'urban-shisha' ); ?>">
				<a href="<?php echo esc_url( urban_shisha_route_url( 'account', array( 'tab' => 'orders' ) ) ); ?>"><?php esc_html_e( 'My orders', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></a>
				<a href="<?php echo esc_url( urban_shisha_route_url( 'account', array( 'tab' => 'addresses' ) ) ); ?>"><?php esc_html_e( 'Addresses', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></a>
				<a href="<?php echo esc_url( $wishlist_link ? $wishlist_link['url'] : urban_shisha_route_url( 'account', array( 'tab' => 'wishlist' ) ) ); ?>" data-open="wishlist"><?php esc_html_e( 'Wishlist', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></a>
				<a href="<?php echo esc_url( urban_shisha_route_url( 'account', array( 'tab' => 'details' ) ) ); ?>"><?php esc_html_e( 'Account details', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></a>
			</nav>
			<a class="account-logout" href="<?php echo esc_url( $logout_url ); ?>"><?php esc_html_e( 'Log out', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></a>
		<?php else : ?>
			<p class="account-menu-intro"><?php esc_html_e( 'Your account and saved favourites, in one place.', 'urban-shisha' ); ?></p>
			<a class="button account-signin" href="<?php echo esc_url( urban_shisha_route_url( 'account' ) ); ?>"><?php esc_html_e( 'Sign in / Register', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></a>
			<nav aria-label="<?php esc_attr_e( 'Account shortcuts', 'urban-shisha' ); ?>">
				<a href="<?php echo esc_url( urban_shisha_route_url( 'account', array( 'tab' => 'orders' ) ) ); ?>"><?php esc_html_e( 'My orders', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></a>
				<a href="<?php echo esc_url( urban_shisha_route_url( 'account', array( 'tab' => 'addresses' ) ) ); ?>"><?php esc_html_e( 'Addresses', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></a>
				<a href="<?php echo esc_url( $wishlist_link ? $wishlist_link['url'] : urban_shisha_route_url( 'account', array( 'tab' => 'wishlist' ) ) ); ?>" data-open="wishlist"><?php esc_html_e( 'Wishlist', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></a>
			</nav>
			<a class="account-menu-help" href="<?php echo esc_url( urban_shisha_route_url( 'contact' ) ); ?>"><?php esc_html_e( 'Need a hand? Contact us', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></a>
		<?php endif; ?>
	</div>
</div>
<a class="bag-button" href="<?php echo esc_url( urban_shisha_route_url( 'cart' ) ); ?>" data-open="cart" aria-label="<?php esc_attr_e( 'Open shopping bag', 'urban-shisha' ); ?>">
	<svg><use href="#i-bag"/></svg>
	<?php echo urban_shisha_cart_count_markup(); ?>
</a>
<button class="icon-button mobile-toggle" aria-label="<?php esc_attr_e( 'Open navigation', 'urban-shisha' ); ?>" aria-expanded="false" aria-controls="mobile-nav"><svg><use href="#i-menu"/></svg></button>
</div>
</div>
<?php
$mega_cards = urban_shisha_header_cards();
$mega_eyebrow = urban_shisha_get_option( 'mega_eyebrow', '' );
$mega_heading = urban_shisha_get_option( 'mega_heading', '' );
$shop_link = urban_shisha_parse_link( urban_shisha_get_option( 'mega_shop_link' ) );
$mega_note = urban_shisha_get_option( 'mega_footer_note', '' );
?>
<?php if ( $mega_cards ) : ?>
<div class="mega-menu" id="category-menu" aria-label="<?php esc_attr_e( 'Product categories', 'urban-shisha' ); ?>" hidden>
<div class="mega-heading"><div>
<?php if ( $mega_eyebrow ) : ?><p class="eyebrow"><?php echo esc_html( $mega_eyebrow ); ?></p><?php endif; ?>
<?php if ( $mega_heading ) : ?><h2><?php echo esc_html( $mega_heading ); ?></h2><?php endif; ?>
</div>
<?php if ( $shop_link ) : ?><a class="text-link" href="<?php echo esc_url( $shop_link['url'] ); ?>"<?php echo $shop_link['target'] ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>><?php echo esc_html( $shop_link['title'] ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></a><?php endif; ?>
</div>
<div class="mega-categories">
<?php foreach ( $mega_cards as $card ) : ?>
<a class="mega-category" href="<?php echo esc_url( $card['url'] ); ?>"<?php echo $card['target'] ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
<span class="mega-photo" aria-hidden="true"><?php if ( $card['image'] ) { echo wp_get_attachment_image( $card['image'], 'medium', false, array( 'class' => 'mega-photo-img', 'alt' => '' ) ); } ?></span>
<span class="mega-label"><?php echo esc_html( $card['label'] ); ?><svg aria-hidden="true"><use href="#i-arrow"/></svg></span>
</a>
<?php endforeach; ?>
</div>
<div class="mega-bottom"><?php urban_shisha_render_header_menu( false, 'header-shortcuts' ); ?><?php if ( $mega_note ) : ?><span><?php echo esc_html( $mega_note ); ?></span><?php endif; ?></div>
</div>
<?php endif; ?>
<nav class="mobile-nav" id="mobile-nav" aria-label="<?php esc_attr_e( 'Mobile navigation', 'urban-shisha' ); ?>" hidden>
<?php if ( $mega_cards ) : ?>
<?php $mobile_heading = urban_shisha_get_option( 'mobile_category_heading', '' ); ?>
<?php if ( $mobile_heading ) : ?><p class="mobile-category-title"><?php echo esc_html( $mobile_heading ); ?></p><?php endif; ?>
<div class="mobile-categories">
<?php foreach ( $mega_cards as $card ) : ?>
<a class="mobile-category" href="<?php echo esc_url( $card['url'] ); ?>"<?php echo $card['target'] ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
<span class="mobile-photo" aria-hidden="true"><?php if ( $card['image'] ) { echo wp_get_attachment_image( $card['image'], 'medium', false, array( 'class' => 'mobile-photo-img', 'alt' => '' ) ); } ?></span>
<span class="mobile-label"><?php echo esc_html( $card['label'] ); ?><svg aria-hidden="true"><use href="#i-arrow"/></svg></span>
</a>
<?php endforeach; ?>
</div>
<?php endif; ?>
<?php urban_shisha_render_mobile_nav_links(); ?>
<button type="button" class="mobile-wishlist-toggle" data-open="wishlist">
	<span><?php esc_html_e( 'Your wishlist', 'urban-shisha' ); ?></span> <svg aria-hidden="true"><use href="#i-heart"/></svg>
</button>
</nav></header>
<?php get_template_part( 'template-parts/header', 'search' ); ?>
<?php get_template_part( 'template-parts/commerce-drawers' ); ?>
