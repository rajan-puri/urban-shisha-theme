/**
 * Urban Shisha — Commerce Drawers (Cart & Wishlist)
 *
 * Implements accessible side drawers for WooCommerce Cart and Wishlist,
 * with real mini-cart data, WC fragment syncing, and guest/user wishlist persistence.
 */

(() => {
	'use strict';

	const config = window.UrbanCommerceDrawers || {};
	const ajaxUrl = config.ajaxUrl || '/wp-admin/admin-ajax.php';
	const wishlistNonce = config.wishlistNonce || '';
	const cartNonce = config.cartNonce || '';
	const isLoggedIn = !!config.isLoggedIn;
	const shopUrl = config.shopUrl || '/shop/';

	const STORAGE_KEY = 'urban_shisha_wishlist';
	const COOKIE_KEY = 'urban_shisha_guest_wishlist';

	// Cache DOM references
	const dialog = document.getElementById('shop-dialog');
	const drawerEyebrow = document.getElementById('drawer-eyebrow');
	const drawerTitle = document.getElementById('drawer-title');
	const cartPanel = document.getElementById('drawer-panel-cart');
	const wishlistPanel = document.getElementById('drawer-panel-wishlist');

	let lastActiveElement = null;
	let currentMode = 'cart';
	let toastTimer = null;

	// Local Wishlist ID management for guests
	function readLocalWishlist() {
		try {
			const raw = localStorage.getItem(STORAGE_KEY);
			if (!raw) return [];
			const parsed = JSON.parse(raw);
			return Array.isArray(parsed) ? parsed.map(Number).filter(n => Number.isInteger(n) && n > 0) : [];
		} catch (e) {
			return [];
		}
	}

	function writeLocalWishlist(ids) {
		const unique = [...new Set(ids.map(Number).filter(n => Number.isInteger(n) && n > 0))].slice(0, 100);
		try {
			localStorage.setItem(STORAGE_KEY, JSON.stringify(unique));
		} catch (e) {}

		// Keep sync cookie for server-side wp_login migration
		try {
			document.cookie = `${COOKIE_KEY}=${encodeURIComponent(unique.join(','))}; path=/; max-age=2592000; SameSite=Lax`;
		} catch (e) {}
	}

	let savedWishlistIds = isLoggedIn
		? (Array.isArray(config.initialWishlist) ? config.initialWishlist.map(Number) : [])
		: readLocalWishlist();

	// Toast helper
	function showToast(message) {
		let toast = document.getElementById('toast');
		if (!toast) {
			toast = document.createElement('div');
			toast.id = 'toast';
			toast.className = 'toast';
			toast.setAttribute('role', 'status');
			toast.setAttribute('aria-live', 'polite');
			toast.setAttribute('aria-atomic', 'true');
			document.body.appendChild(toast);
		}
		toast.textContent = message;
		toast.classList.add('visible');
		clearTimeout(toastTimer);
		toastTimer = setTimeout(() => {
			toast.classList.remove('visible');
		}, 3500);
	}

	// Drawer open / close
	function openDrawer(mode) {
		if (!dialog) return;

		lastActiveElement = document.activeElement;
		currentMode = mode || 'cart';

		if (currentMode === 'cart') {
			if (drawerEyebrow) drawerEyebrow.textContent = 'MAKE IT YOURS.';
			if (drawerTitle) drawerTitle.textContent = 'Your bag.';
			if (cartPanel) cartPanel.hidden = false;
			if (wishlistPanel) wishlistPanel.hidden = true;
		} else {
			if (drawerEyebrow) drawerEyebrow.textContent = 'MAKE IT YOURS.';
			if (drawerTitle) drawerTitle.textContent = 'Your saved edit.';
			if (cartPanel) cartPanel.hidden = true;
			if (wishlistPanel) wishlistPanel.hidden = false;
			loadWishlistContent();
		}

		if (!dialog.open) {
			dialog.showModal();
		}
		document.body.classList.add('is-locked');

		// Focus close button or first interactive element
		const closeBtn = dialog.querySelector('.close-dialog');
		if (closeBtn) {
			setTimeout(() => closeBtn.focus(), 50);
		}
	}

	function closeDrawer() {
		if (!dialog || !dialog.open) return;
		dialog.close();
		document.body.classList.remove('is-locked');
		if (lastActiveElement && typeof lastActiveElement.focus === 'function') {
			lastActiveElement.focus();
		}
	}

	// Update all heart buttons on page
	function updatePageHearts() {
		const buttons = document.querySelectorAll('.save-product, [data-save]');
		buttons.forEach(btn => {
			const id = Number(btn.dataset.save || btn.dataset.productId);
			if (!id) return;
			const isSaved = savedWishlistIds.includes(id);
			btn.classList.toggle('is-saved', isSaved);
			btn.setAttribute('aria-pressed', isSaved ? 'true' : 'false');
		});
	}

	// Update cart count markups
	function updateCartCounts(count) {
		const num = Math.max(0, parseInt(count, 10) || 0);
		const countEls = document.querySelectorAll('.cart-count');
		countEls.forEach(el => {
			el.textContent = num.toString();
			el.setAttribute('aria-label', `${num} items in bag`);
		});
	}

	// Load wishlist content into panel (read-only GET, server-rendered trusted HTML)
	function loadWishlistContent() {
		const container = document.querySelector('.drawer-wishlist-content');
		if (!container) return;

		if (savedWishlistIds.length === 0) {
			container.innerHTML = `
				<div class="empty-state">
					<h3>Keep an eye on your favourites.</h3>
					<p>Tap the heart on a product to save it here for your next visit.</p>
					<a class="button" href="${shopUrl}" data-browse>
						Discover your favourites
						<svg aria-hidden="true"><use href="#i-arrow"/></svg>
					</a>
				</div>`;
			return;
		}

		const params = new URLSearchParams();
		params.append('action', 'urban_shisha_get_wishlist');
		if (!isLoggedIn) {
			params.append('ids', JSON.stringify(savedWishlistIds));
		}

		fetch(`${ajaxUrl}?${params.toString()}`)
			.then(res => res.json())
			.then(data => {
				if (data && data.success) {
					if (data.data.html) {
						container.innerHTML = data.data.html;
					}
					if (Array.isArray(data.data.ids)) {
						savedWishlistIds = data.data.ids.map(Number);
						if (!isLoggedIn) {
							writeLocalWishlist(savedWishlistIds);
						}
						updatePageHearts();
					}
				}
			})
			.catch(() => {});
	}

	// Toggle wishlist product with explicit action and nonce
	function toggleWishlist(productId) {
		const id = Number(productId);
		if (!id) return;

		const currentlySaved = savedWishlistIds.includes(id);
		let clientAction = '';

		if (currentlySaved) {
			savedWishlistIds = savedWishlistIds.filter(item => item !== id);
			clientAction = 'remove';
			showToast('Removed from your wishlist.');
		} else {
			savedWishlistIds = [...savedWishlistIds, id].slice(0, 100);
			clientAction = 'add';
			showToast('Saved to your wishlist.');
		}

		if (!isLoggedIn) {
			writeLocalWishlist(savedWishlistIds);
		}

		updatePageHearts();

		if (dialog && dialog.open && currentMode === 'wishlist') {
			loadWishlistContent();
		}

		// Perform server AJAX write with nonce verification
		const formData = new FormData();
		formData.append('action', 'urban_shisha_toggle_wishlist');
		formData.append('nonce', wishlistNonce);
		formData.append('product_id', id.toString());
		formData.append('client_action', clientAction);
		formData.append('ids', JSON.stringify(savedWishlistIds));

		fetch(ajaxUrl, {
			method: 'POST',
			body: formData,
			credentials: 'same-origin',
		})
			.then(res => res.json())
			.then(data => {
				if (data && data.success && Array.isArray(data.data.ids)) {
					savedWishlistIds = data.data.ids.map(Number);
					if (!isLoggedIn) {
						writeLocalWishlist(savedWishlistIds);
					}
					updatePageHearts();
					if (dialog && dialog.open && currentMode === 'wishlist') {
						const container = document.querySelector('.drawer-wishlist-content');
						if (container && data.data.html) {
							container.innerHTML = data.data.html;
						}
					}
				}
			})
			.catch(() => {});
	}

	// Update Cart quantity
	function updateCartQty(cartItemKey, delta) {
		if (!cartItemKey) return;

		const formData = new FormData();
		formData.append('action', 'urban_shisha_update_cart_qty');
		formData.append('nonce', cartNonce);
		formData.append('cart_item_key', cartItemKey);
		formData.append('delta', delta.toString());

		fetch(ajaxUrl, {
			method: 'POST',
			body: formData,
			credentials: 'same-origin',
		})
			.then(res => res.json())
			.then(data => {
				if (data && data.success) {
					const container = document.querySelector('.drawer-cart-content');
					if (container && data.data.html) {
						container.innerHTML = data.data.html;
					}
					updateCartCounts(data.data.count);
					triggerWooRefresh();
				}
			})
			.catch(() => {});
	}

	// Remove Cart item
	function removeCartItem(cartItemKey) {
		if (!cartItemKey) return;

		const formData = new FormData();
		formData.append('action', 'urban_shisha_remove_cart_item');
		formData.append('nonce', cartNonce);
		formData.append('cart_item_key', cartItemKey);

		fetch(ajaxUrl, {
			method: 'POST',
			body: formData,
			credentials: 'same-origin',
		})
			.then(res => res.json())
			.then(data => {
				if (data && data.success) {
					const container = document.querySelector('.drawer-cart-content');
					if (container && data.data.html) {
						container.innerHTML = data.data.html;
					}
					updateCartCounts(data.data.count);
					showToast('Item removed from bag.');
					triggerWooRefresh();
				}
			})
			.catch(() => {});
	}

	// Trigger WooCommerce fragment refresh if available
	function triggerWooRefresh() {
		if (window.jQuery && typeof window.jQuery(document.body).trigger === 'function') {
			window.jQuery(document.body).trigger('wc_fragment_refresh');
		}
	}

	// Global Event Delegation
	document.addEventListener('click', e => {
		// Open Cart drawer
		const cartTrigger = e.target.closest('[data-open="cart"]');
		if (cartTrigger) {
			e.preventDefault();
			openDrawer('cart');
			return;
		}

		// Open Wishlist drawer
		const wishlistTrigger = e.target.closest('[data-open="wishlist"]');
		if (wishlistTrigger) {
			e.preventDefault();
			openDrawer('wishlist');
			return;
		}

		// Close Dialog button
		const closeBtn = e.target.closest('.close-dialog');
		if (closeBtn && dialog && dialog.contains(closeBtn)) {
			e.preventDefault();
			closeDrawer();
			return;
		}

		// Save / Wishlist Heart button
		const heartBtn = e.target.closest('.save-product, [data-save]');
		if (heartBtn) {
			e.preventDefault();
			const id = Number(heartBtn.dataset.save || heartBtn.dataset.productId);
			if (id) {
				toggleWishlist(id);
			}
			return;
		}

		// Quantity button in cart drawer
		const qtyBtn = e.target.closest('.qty-btn');
		if (qtyBtn && dialog && dialog.contains(qtyBtn)) {
			e.preventDefault();
			const key = qtyBtn.dataset.quantityKey;
			const delta = parseInt(qtyBtn.dataset.delta, 10) || 0;
			if (key && delta) {
				updateCartQty(key, delta);
			}
			return;
		}

		// Remove button in cart drawer
		const removeBtn = e.target.closest('.remove-item');
		if (removeBtn && dialog && dialog.contains(removeBtn)) {
			e.preventDefault();
			const key = removeBtn.dataset.removeKey;
			if (key) {
				removeCartItem(key);
			}
			return;
		}

		// Browse button inside empty state
		const browseBtn = e.target.closest('[data-browse]');
		if (browseBtn && dialog && dialog.contains(browseBtn)) {
			closeDrawer();
			return;
		}
	});

	// Backdrop click on dialog
	if (dialog) {
		dialog.addEventListener('click', e => {
			if (e.target === dialog) {
				const rect = dialog.getBoundingClientRect();
				if (e.clientX < rect.left || e.clientX > rect.right || e.clientY < rect.top || e.clientY > rect.bottom) {
					closeDrawer();
				}
			}
		});

		dialog.addEventListener('cancel', e => {
			e.preventDefault();
			closeDrawer();
		});

		// Accessible keyboard trap
		dialog.addEventListener('keydown', e => {
			if (e.key === 'Escape') {
				e.preventDefault();
				closeDrawer();
				return;
			}

			if (e.key === 'Tab') {
				const focusables = dialog.querySelectorAll('button:not([disabled]), [href]:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])');
				if (focusables.length === 0) return;

				const first = focusables[0];
				const last = focusables[focusables.length - 1];

				if (e.shiftKey && document.activeElement === first) {
					e.preventDefault();
					last.focus();
				} else if (!e.shiftKey && document.activeElement === last) {
					e.preventDefault();
					first.focus();
				}
			}
		});
	}

	// WooCommerce cart fragments sync
	if (window.jQuery) {
		window.jQuery(document.body).on('added_to_cart removed_from_cart wc_fragments_refreshed wc_fragments_loaded', (event, fragments) => {
			if (fragments && fragments['div.drawer-cart-content']) {
				const container = document.querySelector('.drawer-cart-content');
				if (container) {
					container.outerHTML = fragments['div.drawer-cart-content'];
				}
			}
		});
	}

	// Initial sync on page load
	document.addEventListener('DOMContentLoaded', () => {
		updatePageHearts();

		// If logged in and guest IDs exist in localStorage, merge via nonce-protected POST
		// Only clear guest IDs after server successfully acknowledges!
		if (isLoggedIn) {
			const guestIds = readLocalWishlist();
			if (guestIds.length > 0) {
				const formData = new FormData();
				formData.append('action', 'urban_shisha_sync_wishlist');
				formData.append('nonce', wishlistNonce);
				formData.append('ids', JSON.stringify(guestIds));

				fetch(ajaxUrl, {
					method: 'POST',
					body: formData,
					credentials: 'same-origin',
				})
					.then(res => res.json())
					.then(data => {
						if (data && data.success && Array.isArray(data.data.ids)) {
							writeLocalWishlist([]);
							savedWishlistIds = data.data.ids.map(Number);
							updatePageHearts();
							if (dialog && dialog.open && currentMode === 'wishlist' && data.data.html) {
								const container = document.querySelector('.drawer-wishlist-content');
								if (container) {
									container.innerHTML = data.data.html;
								}
							}
						}
					})
					.catch(() => {});
			}
		}
	});

	// Public API for theme extensibility
	window.UrbanCommerce = {
		openDrawer,
		closeDrawer,
		toggleWishlist,
		showToast,
		getWishlist: () => [...savedWishlistIds],
	};
})();
