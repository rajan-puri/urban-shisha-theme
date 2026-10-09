/**
 * Urban Shisha — WordPress Live Cart Integration
 *
 * Connects native WooCommerce cart endpoints, live quantity steppers, item removal,
 * coupon apply/remove, and synchronizes header & drawer counts.
 */
(() => {
	'use strict';

	const config = window.UrbanCartConfig || window.UrbanCommerceDrawers || {};
	const ajaxUrl = config.ajaxUrl || '/wp-admin/admin-ajax.php';
	const cartNonce = config.cartNonce || '';
	let toastTimer = null;

	const $ = (s, r = document) => r.querySelector(s);
	const $$ = (s, r = document) => [...r.querySelectorAll(s)];

	function showToast(message) {
		let toast = $('#toast');
		if (!toast) {
			toast = document.createElement('div');
			toast.id = 'toast';
			toast.className = 'toast';
			toast.setAttribute('role', 'status');
			toast.setAttribute('aria-live', 'polite');
			document.body.appendChild(toast);
		}
		toast.textContent = message;
		toast.classList.add('visible');
		clearTimeout(toastTimer);
		toastTimer = setTimeout(() => {
			toast.classList.remove('visible');
		}, 3200);
	}

	function updateHeaderCounts(count) {
		$$('.cart-count').forEach(el => {
			el.textContent = count;
		});
		const bagBtn = $('.bag-button');
		if (bagBtn) {
			bagBtn.setAttribute('aria-label', `Open shopping bag, ${count} items`);
		}
	}

	// Quantity controls
	document.addEventListener('click', async e => {
		const qtyBtn = e.target.closest('[data-cart-qty]');
		if (qtyBtn) {
			e.preventDefault();
			const key = qtyBtn.getAttribute('data-cart-qty');
			const delta = parseInt(qtyBtn.getAttribute('data-delta') || '0', 10);
			const input = $(`[data-input-qty="${key}"]`);
			if (!input) return;

			const currentVal = parseInt(input.value || '1', 10);
			input.dataset.prevQty = currentVal;
			const newVal = Math.max(1, Math.min(99, currentVal + delta));
			if (newVal === currentVal) return;

			input.value = newVal;
			await updateCartItemQuantity(key, newVal);
			return;
		}

		// Remove button
		const removeBtn = e.target.closest('[data-cart-remove]');
		if (removeBtn) {
			e.preventDefault();
			const key = removeBtn.getAttribute('data-cart-remove');
			await removeCartItem(key);
			return;
		}

		// Save to wishlist button
		const saveBtn = e.target.closest('[data-cart-save]');
		if (saveBtn) {
			e.preventDefault();
			const productId = parseInt(saveBtn.getAttribute('data-cart-save'), 10);
			if (productId) {
				if (window.UrbanCommerce && typeof window.UrbanCommerce.toggleWishlist === 'function') {
					window.UrbanCommerce.toggleWishlist(productId);
					const isSaved = (window.UrbanCommerce.getWishlist() || []).includes(productId);
					saveBtn.classList.toggle('is-saved', isSaved);
					saveBtn.setAttribute('aria-pressed', isSaved ? 'true' : 'false');
					const span = saveBtn.querySelector('span');
					if (span) {
						span.textContent = isSaved ? 'Saved to wishlist' : 'Save to wishlist';
					}
				} else if (window.UrbanWishlist && typeof window.UrbanWishlist.toggle === 'function') {
					window.UrbanWishlist.toggle(productId);
					const isSaved = saveBtn.classList.toggle('is-saved');
					saveBtn.setAttribute('aria-pressed', isSaved ? 'true' : 'false');
					const span = saveBtn.querySelector('span');
					if (span) {
						span.textContent = isSaved ? 'Saved to wishlist' : 'Save to wishlist';
					}
					showToast(isSaved ? 'Item saved to your wishlist' : 'Item removed from wishlist');
				}
			}
			return;
		}

		// Clear bag button
		const clearBtn = e.target.closest('#cart-clear');
		if (clearBtn) {
			e.preventDefault();
			const rows = $$('[data-cart-item-key]');
			for (const row of rows) {
				const key = row.getAttribute('data-cart-item-key');
				if (key) {
					await removeCartItem(key, false);
				}
			}
			window.location.reload();
			return;
		}

		// Remove coupon
		const removeCouponBtn = e.target.closest('.remove-coupon-btn');
		if (removeCouponBtn) {
			e.preventDefault();
			const code = removeCouponBtn.getAttribute('data-coupon');
			if (code) {
				await removeCoupon(code);
			}
			return;
		}
	});

	// Track previous quantity before manual edit
	document.addEventListener('focusin', e => {
		const input = e.target.closest('[data-input-qty]');
		if (input) {
			input.dataset.prevQty = input.value;
		}
	});

	// Input quantity change on blur or enter
	document.addEventListener('change', async e => {
		const input = e.target.closest('[data-input-qty]');
		if (input) {
			const key = input.getAttribute('data-input-qty');
			const val = Math.max(1, Math.min(99, parseInt(input.value || '1', 10)));
			input.value = val;
			await updateCartItemQuantity(key, val);
		}
	});

	async function updateCartItemQuantity(key, quantity) {
		const row = $(`#cart-row-${key}`);
		if (row) {
			row.style.opacity = '0.6';
		}

		try {
			const formData = new FormData();
			formData.append('action', 'urban_shisha_cart_update_quantity');
			formData.append('nonce', cartNonce);
			formData.append('cart_item_key', key);
			formData.append('quantity', quantity);

			const res = await fetch(ajaxUrl, { credentials: 'same-origin', method: 'POST', body: formData });
			const data = await res.json();

			if (data.success) {
				const input = $(`[data-input-qty="${key}"]`);
				if (input) {
					input.dataset.prevQty = quantity;
				}

				const lineTotalEl = $(`#line-total-${key}`);
				if (lineTotalEl && data.data.line_total) {
					lineTotalEl.innerHTML = data.data.line_total;
				}

				if (data.data.subtotal) {
					const subtotalEl = $('#summary-subtotal');
					if (subtotalEl) subtotalEl.innerHTML = data.data.subtotal;
				}

				if (data.data.total) {
					const totalEl = $('#summary-total');
					const mobileBarTotal = $('#mobile-bar-total');
					if (totalEl) totalEl.innerHTML = data.data.total;
					if (mobileBarTotal) mobileBarTotal.innerHTML = data.data.total;
				}

				const count = data.data.cart_count || 0;
				updateHeaderCounts(count);

				const cartCountBadge = $('#cart-item-count');
				const summaryItemCount = $('#summary-item-count');
				const itemLabel = `${count} ${count === 1 ? 'item' : 'items'}`;
				if (cartCountBadge) cartCountBadge.textContent = `${itemLabel} in your bag`;
				if (summaryItemCount) summaryItemCount.textContent = itemLabel;

				// Update minus/plus buttons state
				const minusBtn = $(`[data-cart-qty="${key}"][data-delta="-1"]`);
				if (minusBtn) minusBtn.disabled = quantity <= 1;

				showToast('Bag updated');

				// Trigger fragment update
				if (window.jQuery) {
					window.jQuery(document.body).trigger('wc_fragment_refresh');
				}
			} else {
				const input = $(`[data-input-qty="${key}"]`);
				if (input) {
					const restoredQty = (data.data && typeof data.data.current_qty !== 'undefined')
						? data.data.current_qty
						: (input.dataset.prevQty || 1);
					input.value = restoredQty;
					input.dataset.prevQty = restoredQty;
				}
				showToast(data.data?.message || 'Could not update bag');
			}
		} catch (err) {
			const input = $(`[data-input-qty="${key}"]`);
			if (input && input.dataset.prevQty) {
				input.value = input.dataset.prevQty;
			}
			showToast('Network error updating bag');
		} finally {
			if (row) {
				row.style.opacity = '1';
			}
		}
	}

	async function removeCartItem(key, reloadIfEmpty = true) {
		const row = $(`#cart-row-${key}`);
		if (row) {
			row.style.opacity = '0.4';
		}

		try {
			const formData = new FormData();
			formData.append('action', 'urban_shisha_cart_remove_item');
			formData.append('nonce', cartNonce);
			formData.append('cart_item_key', key);

			const res = await fetch(ajaxUrl, { credentials: 'same-origin', method: 'POST', body: formData });
			const data = await res.json();

			if (data.success) {
				if (row) row.remove();

				const count = data.data.cart_count || 0;
				updateHeaderCounts(count);

				if (data.data.is_empty && reloadIfEmpty) {
					window.location.reload();
					return;
				}

				if (data.data.subtotal) {
					const subtotalEl = $('#summary-subtotal');
					if (subtotalEl) subtotalEl.innerHTML = data.data.subtotal;
				}

				if (data.data.total) {
					const totalEl = $('#summary-total');
					const mobileBarTotal = $('#mobile-bar-total');
					if (totalEl) totalEl.innerHTML = data.data.total;
					if (mobileBarTotal) mobileBarTotal.innerHTML = data.data.total;
				}

				const cartCountBadge = $('#cart-item-count');
				const summaryItemCount = $('#summary-item-count');
				const itemLabel = `${count} ${count === 1 ? 'item' : 'items'}`;
				if (cartCountBadge) cartCountBadge.textContent = `${itemLabel} in your bag`;
				if (summaryItemCount) summaryItemCount.textContent = itemLabel;

				showToast('Item removed from bag');

				if (window.jQuery) {
					window.jQuery(document.body).trigger('wc_fragment_refresh');
				}
			} else {
				showToast(data.data?.message || 'Could not remove item');
			}
		} catch (err) {
			showToast('Network error removing item');
		}
	}

	// Apply coupon
	const couponApplyBtn = $('#cart-coupon-apply');
	const couponInput = $('#cart-coupon-code');
	const couponFeedback = $('#coupon-feedback');

	if (couponApplyBtn && couponInput) {
		couponApplyBtn.addEventListener('click', async e => {
			e.preventDefault();
			const code = couponInput.value.trim();
			if (!code) {
				showToast('Please enter a promo code');
				return;
			}

			couponApplyBtn.disabled = true;
			try {
				const formData = new FormData();
				formData.append('action', 'urban_shisha_apply_coupon');
				formData.append('nonce', cartNonce);
				formData.append('coupon_code', code);

				const res = await fetch(ajaxUrl, { credentials: 'same-origin', method: 'POST', body: formData });
				const data = await res.json();

				if (data.success) {
					showToast('Promo code applied!');
					window.location.reload();
				} else {
					if (couponFeedback) {
						couponFeedback.textContent = data.data?.message || 'Invalid promo code.';
						couponFeedback.style.color = 'var(--pop)';
						couponFeedback.hidden = false;
					}
					showToast(data.data?.message || 'Invalid promo code');
				}
			} catch (err) {
				showToast('Error applying coupon');
			} finally {
				couponApplyBtn.disabled = false;
			}
		});

		couponInput.addEventListener('keydown', e => {
			if (e.key === 'Enter') {
				e.preventDefault();
				couponApplyBtn.click();
			}
		});
	}

	async function removeCoupon(code) {
		try {
			const formData = new FormData();
			formData.append('action', 'urban_shisha_remove_coupon');
			formData.append('nonce', cartNonce);
			formData.append('coupon_code', code);

			const res = await fetch(ajaxUrl, { credentials: 'same-origin', method: 'POST', body: formData });
			const data = await res.json();

			if (data.success) {
				showToast('Coupon removed');
				window.location.reload();
			} else {
				showToast(data.data?.message || 'Could not remove coupon');
			}
		} catch (err) {
			showToast('Network error');
		}
	}

	// Sync wishlist heart states on cart page load
	if (window.UrbanCommerce && typeof window.UrbanCommerce.updatePageHearts === 'function') {
		window.UrbanCommerce.updatePageHearts();
	}
})();
