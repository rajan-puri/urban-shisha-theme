/**
 * Urban Shisha — Shop Page Controller (WordPress & WooCommerce)
 *
 * Implements server-backed catalog querying, dynamic pagination,
 * synchronized filters, live search, mobile dialog, and drawer integration.
 */
(() => {
	'use strict';

	const config = window.UrbanShopConfig || {};
	const ajaxUrl = config.ajaxUrl || '/wp-admin/admin-ajax.php';
	const nonce = config.nonce || '';
	const cartNonce = config.cartNonce || '';
	const shopUrl = config.shopUrl || '/shop/';
    const archive = config.archiveContext || {};
    const archiveCategories = archive.archive_category ? [archive.archive_category] : [];
    const archiveBrands = archive.archive_brand ? [archive.archive_brand] : [];
    const safeNumber = (raw, fallback) => raw !== null && raw !== '' && Number.isFinite(Number(raw)) ? Math.max(0, Number(raw)) : fallback;
	const priceMinLimit = typeof config.priceMin === 'number' ? config.priceMin : 0;
	const priceMaxLimit = typeof config.priceMax === 'number' ? config.priceMax : 30000;

	// DOM Elements
	const form = document.getElementById('shop-filters');
	const grid = document.getElementById('shop-products');
	const paginationContainer = document.getElementById('shop-pagination');
	const resultCountEl = document.getElementById('result-count');
	const catalogTitleEl = document.getElementById('catalog-title');
	const searchInput = document.getElementById('shop-search');
	const sortSelect = document.getElementById('shop-sort');
	const filterOpenBtn = document.getElementById('filter-open');
	const filterTotalBadge = document.getElementById('filter-total');
	const activeFiltersContainer = document.getElementById('active-filters');
	const emptyStateEl = document.getElementById('shop-empty');
	const endNoteEl = document.querySelector('.shop-end-note');
	const filterDialog = document.getElementById('filter-dialog');
	const mobileFilterContent = document.getElementById('mobile-filter-content');
	const sidebarPanel = document.getElementById('filter-panel');
	const minInput = document.getElementById('price-min');
	const maxInput = document.getElementById('price-max');
	const rangeSlider = document.getElementById('price-range');
	const priceNote = document.getElementById('price-note');
	const categoryTabs = document.querySelectorAll('.shop-category-tabs a');

	// State
	const state = {
		category: [],
		brand: [],
		min: priceMinLimit,
		max: priceMaxLimit,
		q: '',
		sort: 'featured',
		new: false,
		paged: 1,
	};

	let isFetching = false;
	let searchDebounceTimer = null;
	let priceDebounceTimer = null;
	let activeAbortController = null;
    let requestSequence = 0;
    const requestStatus = document.createElement('div');
    requestStatus.className = 'shop-request-status'; requestStatus.hidden = true; requestStatus.setAttribute('role', 'alert');
    const errorText = document.createElement('p'); const retryButton = document.createElement('button');
    retryButton.type = 'button'; retryButton.className = 'text-link'; retryButton.textContent = 'Try again';
    retryButton.addEventListener('click', () => fetchFilteredCatalogue());
    requestStatus.append(errorText, retryButton); grid?.before(requestStatus);

	const moneyFormatter = new Intl.NumberFormat('en-IN', {
		style: 'currency',
		currency: 'INR',
		maximumFractionDigits: 2,
	});

	function formatMoney(num) {
		return moneyFormatter.format(num);
	}

	function isReducedMotion() {
		return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	}

	// Read state from URL search params
	function parseURLState() {
		const searchParams = new URLSearchParams(window.location.search);

		// Categories
		let cats = [];
		if (searchParams.has('category')) {
			cats = searchParams.get('category').split(',').map(s => s.trim()).filter(Boolean);
		}
		state.category = cats.length ? cats : [...archiveCategories];

		// Brands
		let brands = [];
		if (searchParams.has('brand')) {
			brands = searchParams.get('brand').split(',').map(s => s.trim()).filter(Boolean);
		}
		state.brand = brands.length ? brands : [...archiveBrands];

		// Price Range
		const rawMin = searchParams.get('min_price') ?? searchParams.get('min');
		const rawMax = searchParams.get('max_price') ?? searchParams.get('max');

        state.min = safeNumber(rawMin, priceMinLimit);
        state.max = safeNumber(rawMax, priceMaxLimit);
        if(state.min > state.max) [state.min, state.max] = [state.max, state.min];

		// Search
		state.q = (searchParams.get('q') || '').trim();

		// Sort
		const validSorts = ['featured', 'price-low', 'price-high', 'name', 'date'];
		const currentSort = searchParams.get('sort');
		state.sort = validSorts.includes(currentSort) ? currentSort : 'featured';

		// New Badge
		state.new = searchParams.get('new') === '1' || searchParams.get('new') === 'true';

		// Page
		state.paged = Math.max(1, parseInt(searchParams.get('paged'), 10) || Number(window.location.pathname.match(/\/page\/(\d+)\/?$/)?.[1]) || 1);

		syncInputsFromState();
	}

	// Update DOM form inputs to reflect state
	function syncInputsFromState() {
		// Category Checkboxes
		document.querySelectorAll('input[name="category"]').forEach(cb => {
			cb.checked = state.category.includes(cb.value);
		});

		// Brand Checkboxes
		document.querySelectorAll('input[name="brand"]').forEach(cb => {
			cb.checked = state.brand.includes(cb.value);
		});

		// "New" Checkbox
		const newCb = document.querySelector('input[name="new"]');
		if (newCb) {
			newCb.checked = state.new;
		}

		// Price Inputs
		if (minInput) minInput.value = state.min;
		if (maxInput) maxInput.value = state.max;
		if (rangeSlider) { rangeSlider.max = Math.max(priceMaxLimit, state.max); rangeSlider.value = state.max; }
		if (priceNote) {
			priceNote.textContent = state.min > 0
				? `${formatMoney(state.min)} – ${formatMoney(state.max)}`
				: `Up to ${formatMoney(state.max)}`;
		}

		// Search
		if (searchInput) searchInput.value = state.q;

		// Sort
		if (sortSelect) sortSelect.value = state.sort;

		// Category Tabs
		categoryTabs.forEach(tab => {
			const href = tab.getAttribute('href');
			if (!href) return;
			try {
				const tabUrl = new URL(href, window.location.origin);
				const tabCat = tabUrl.searchParams.get('category');
				const isActive = tabCat
					? (state.category.length === 1 && state.category[0] === tabCat)
					: (state.category.length === 0);
				if (isActive) {
					tab.setAttribute('aria-current', 'page');
				} else {
					tab.removeAttribute('aria-current');
				}
			} catch (e) {}
		});

		renderActiveFilterChips();
	}

	// Render Active Filter Chips in #active-filters
	function renderActiveFilterChips() {
		if (!activeFiltersContainer) return;

		const chips = [];

		// Categories
		state.category.forEach(catSlug => {
			const labelEl = document.querySelector(`input[name="category"][value="${CSS.escape(catSlug)}"]`);
			const label = labelEl ? (labelEl.closest('.filter-option')?.querySelector('span')?.textContent || catSlug) : catSlug;
			chips.push({
				type: 'category',
				value: catSlug,
				label: label.trim(),
			});
		});

		// Brands
		state.brand.forEach(brandSlug => {
			const labelEl = document.querySelector(`input[name="brand"][value="${CSS.escape(brandSlug)}"]`);
			const label = labelEl ? (labelEl.closest('.filter-option')?.querySelector('span')?.textContent || brandSlug) : brandSlug;
			chips.push({
				type: 'brand',
				value: brandSlug,
				label: label.trim(),
			});
		});

		// Price
		if (state.min > 0 || state.max < priceMaxLimit) {
			chips.push({
				type: 'price',
				value: 'price',
				label: state.min > 0
					? `${formatMoney(state.min)} – ${formatMoney(state.max)}`
					: `Up to ${formatMoney(state.max)}`,
			});
		}

		// Search
		if (state.q) {
			chips.push({
				type: 'q',
				value: state.q,
				label: `“${state.q}”`,
			});
		}

		// New
		if (state.new) {
			chips.push({
				type: 'new',
				value: '1',
				label: 'New arrivals',
			});
		}

		if (filterTotalBadge) {
			filterTotalBadge.textContent = chips.length.toString();
		}

		if (chips.length === 0) {
			activeFiltersContainer.innerHTML = '';
			return;
		}

		let html = chips.map(chip => `
			<button type="button" class="filter-chip" data-remove-type="${chip.type}" data-remove-val="${chip.value}" aria-label="Remove filter: ${chip.label}">
				<span>${chip.label}</span>
				<svg aria-hidden="true" width="12" height="12"><use href="#i-close"/></svg>
			</button>
		`).join('');

		html += `<button type="button" class="filter-chip" data-clear aria-label="Clear all filters">Clear all</button>`;

		activeFiltersContainer.innerHTML = html;
	}

	// Update browser URL (without reload)
	function updateBrowserURL(push = true) {
		const url = new URL(window.location.href);
		const params = url.searchParams;
        params.delete('min'); params.delete('max');

		if (state.category.length > 0) {
			params.set('category', state.category.join(','));
		} else {
			params.delete('category');
		}

		if (state.brand.length > 0) {
			params.set('brand', state.brand.join(','));
		} else {
			params.delete('brand');
		}

		if (state.min > 0) {
			params.set('min_price', state.min.toString());
		} else {
			params.delete('min_price');
			params.delete('min');
		}

		if (state.max < priceMaxLimit) {
			params.set('max_price', state.max.toString());
		} else {
			params.delete('max_price');
			params.delete('max');
		}

		if (state.q) {
			params.set('q', state.q);
		} else {
			params.delete('q');
		}

		if (state.sort && state.sort !== 'featured') {
			params.set('sort', state.sort);
		} else {
			params.delete('sort');
		}

		if (state.new) {
			params.set('new', '1');
		} else {
			params.delete('new');
		}

		if (state.paged > 1) {
			params.set('paged', state.paged.toString());
		} else {
			params.delete('paged');
		}

		const newRelativePathQuery = url.pathname.replace(/\/page\/\d+\/?$/, '/') + (params.toString() ? '?' + params.toString() : '');
		if (push) {
			window.history.pushState(null, '', newRelativePathQuery);
		} else {
			window.history.replaceState(null, '', newRelativePathQuery);
		}
	}

	// Fetch filtered catalogue from WordPress backend
	async function fetchFilteredCatalogue(scrollUp = false) {
        const requestId = ++requestSequence;
		if (activeAbortController) {
			activeAbortController.abort();
		}
		activeAbortController = new AbortController();

		if (grid) {
			grid.style.opacity = '0.55';
			grid.style.pointerEvents = 'none';
		}
		isFetching = true;
        grid?.setAttribute('aria-busy', 'true'); requestStatus.hidden = true;

		const bodyData = new FormData();
		bodyData.append('action', 'urban_shisha_shop_filter');
		bodyData.append('nonce', nonce);
		if (state.category.length) bodyData.append('category', state.category.join(','));
		if (state.brand.length) bodyData.append('brand', state.brand.join(','));
		if (state.min > 0) bodyData.append('min_price', state.min.toString());
		if (state.max < priceMaxLimit) bodyData.append('max_price', state.max.toString());
		if (state.q) bodyData.append('q', state.q);
		if (state.sort) bodyData.append('sort', state.sort);
		if (state.new) bodyData.append('new', '1');
		bodyData.append('paged', state.paged.toString());
        if(archive.archive_category) bodyData.append('archive_category', archive.archive_category);
        if(archive.archive_brand) bodyData.append('archive_brand', archive.archive_brand);

		try {
			const res = await fetch(ajaxUrl, {
				method: 'POST',
				body: bodyData,
				credentials: 'same-origin',
				signal: activeAbortController.signal,
			});

			if (!res.ok) {
				throw new Error('Server responded with error');
			}

			const data = await res.json();
			if (!data || !data.success) {
				throw new Error(data?.data?.message || 'Failed to update results');
			}

			const payload = data.data;
            if(requestId !== requestSequence) return;
            grid.hidden = false;
            if(Number(payload.paged) !== state.paged) { state.paged = Number(payload.paged); updateBrowserURL(false); }

			// Update Products Grid
			if (grid) {
				grid.innerHTML = payload.html || '';
			}

			// Update Pagination
			const currentPagination = document.getElementById('shop-pagination');
			if (currentPagination) {
				currentPagination.outerHTML = payload.pagination_html || '<div id="shop-pagination"></div>';
			} else if (grid && payload.pagination_html) {
				grid.insertAdjacentHTML('afterend', payload.pagination_html);
			}

			// Update Result Count
			if (resultCountEl && payload.count_text) {
				resultCountEl.textContent = payload.count_text;
			}

			// Update Catalog Title
			if (catalogTitleEl && payload.title) {
				catalogTitleEl.textContent = payload.title;
                const punct = document.createElement('span'); punct.className = 'punct'; punct.textContent = '.'; catalogTitleEl.append(punct);
			}

			// Update Empty State
			if (emptyStateEl) {
				emptyStateEl.hidden = !payload.empty;
			}
			if (endNoteEl) {
				endNoteEl.hidden = payload.empty;
			}

			// Sync Wishlist Hearts on newly rendered cards
			if (window.UrbanCommerce?.updatePageHearts) {
				window.UrbanCommerce.updatePageHearts();
			}

			// Scroll to catalog top if requested (e.g. pagination or tab switch)
			if (scrollUp) {
				const catalogEl = document.getElementById('catalog');
				if (catalogEl) {
					catalogEl.scrollIntoView({ behavior: isReducedMotion() ? 'auto' : 'smooth' });
				}
			}

			// GSAP Entrance
			if (grid && window.gsap && !isReducedMotion() && grid.children.length > 0) {
				window.gsap.fromTo(
					grid.children,
					{ y: 15, opacity: 0 },
					{ y: 0, opacity: 1, duration: 0.35, stagger: 0.02, ease: 'power2.out', clearProps: 'transform,opacity' }
				);
			}
		} catch (err) {
			if (err.name !== 'AbortError' && requestId === requestSequence) {
				grid.hidden = true; if(emptyStateEl)emptyStateEl.hidden = true; if(endNoteEl)endNoteEl.hidden = true;
                errorText.textContent = 'Products could not be loaded. Please try again.'; requestStatus.hidden = false;
				if (window.UrbanCommerce?.showToast) {
					window.UrbanCommerce.showToast('Unable to load products. Please check connection.');
				}
			}
		} finally {
            if(requestId !== requestSequence) return;
            grid?.setAttribute('aria-busy', 'false');
			if (grid) {
				grid.style.opacity = '';
				grid.style.pointerEvents = '';
			}
			isFetching = false;
		}
	}

	// Filter change triggers
	function triggerFilterChange(resetPage = true, pushHistory = true, scrollUp = false) {
		if (resetPage) {
			state.paged = 1;
		}
		updateBrowserURL(pushHistory);
		syncInputsFromState();
		fetchFilteredCatalogue(scrollUp);
	}

	// Reset all filters
	function clearAllFilters() {
		state.category = [...archiveCategories];
		state.brand = [...archiveBrands];
		state.min = priceMinLimit;
		state.max = priceMaxLimit;
		state.q = '';
		state.sort = 'featured';
		state.new = false;
		state.paged = 1;
		triggerFilterChange(true, true, true);
	}

	// Event Listeners Initialization
	function initListeners() {
		// Prevent default form submit
		form?.addEventListener('submit', e => {
			e.preventDefault();
		});

		// Checkbox changes (category, brand, new)
		form?.addEventListener('change', e => {
			const target = e.target;
			if (target.name === 'category') {
				const checked = [...document.querySelectorAll('input[name="category"]:checked')].map(cb => cb.value);
				state.category = checked;
				triggerFilterChange(true);
			} else if (target.name === 'brand') {
				const checked = [...document.querySelectorAll('input[name="brand"]:checked')].map(cb => cb.value);
				state.brand = checked;
				triggerFilterChange(true);
			} else if (target.name === 'new') {
				state.new = target.checked;
				triggerFilterChange(true);
			}
		});

		// Numeric price inputs
		const handlePriceInputChange = () => {
            let minVal = safeNumber(minInput?.value, priceMinLimit);
            let maxVal = safeNumber(maxInput?.value, priceMaxLimit);
            if(minVal > maxVal) [minVal, maxVal] = [maxVal, minVal];
            state.min = minVal; state.max = maxVal;
            if(minInput) minInput.value = minVal; if(maxInput) maxInput.value = maxVal;

			if (rangeSlider) { rangeSlider.max = Math.max(priceMaxLimit, state.max); rangeSlider.value = state.max; }
			if (priceNote) {
				priceNote.textContent = state.min > 0
					? `${formatMoney(state.min)} – ${formatMoney(state.max)}`
					: `Up to ${formatMoney(state.max)}`;
			}

			clearTimeout(priceDebounceTimer);
			priceDebounceTimer = setTimeout(() => {
				triggerFilterChange(true);
			}, 350);
		};

		minInput?.addEventListener('input', handlePriceInputChange);
		maxInput?.addEventListener('input', handlePriceInputChange);

		// Range slider
		rangeSlider?.addEventListener('input', e => {
			const val = parseFloat(e.target.value) || priceMaxLimit;
			state.max = val;
			if (state.min > state.max) {
				state.min = 0;
				if (minInput) minInput.value = '0';
			}
			if (maxInput) maxInput.value = state.max;
			if (priceNote) {
				priceNote.textContent = state.min > 0
					? `${formatMoney(state.min)} – ${formatMoney(state.max)}`
					: `Up to ${formatMoney(state.max)}`;
			}

			clearTimeout(priceDebounceTimer);
			priceDebounceTimer = setTimeout(() => {
				triggerFilterChange(true);
			}, 250);
		});

		// Live Search input with debounce
		searchInput?.addEventListener('input', e => {
			state.q = (e.target.value || '').trim();
			clearTimeout(searchDebounceTimer);
			searchDebounceTimer = setTimeout(() => {
				triggerFilterChange(true);
			}, 300);
		});

		// Sort dropdown
		sortSelect?.addEventListener('change', e => {
			state.sort = e.target.value;
			triggerFilterChange(true);
		});

		// Category Tabs Click
		categoryTabs.forEach(tab => {
			tab.addEventListener('click', e => {
                if(archiveCategories.length || archiveBrands.length) return;
				e.preventDefault();
				const href = tab.getAttribute('href');
				try {
					const tabUrl = new URL(href, window.location.origin);
					const cat = tabUrl.searchParams.get('category');
					state.category = cat ? [cat] : [];
					triggerFilterChange(true, true, true);
				} catch (err) {}
			});
		});

		// Active filter chips removal & clear all
		document.addEventListener('click', e => {
			// Chip removal
			const removeBtn = e.target.closest('[data-remove-type]');
			if (removeBtn) {
				e.preventDefault();
				const type = removeBtn.dataset.removeType;
				const val = removeBtn.dataset.removeVal;

				if (type === 'category') {
					state.category = state.category.filter(c => c !== val);
				} else if (type === 'brand') {
					state.brand = state.brand.filter(b => b !== val);
				} else if (type === 'price') {
					state.min = priceMinLimit;
					state.max = priceMaxLimit;
				} else if (type === 'q') {
					state.q = '';
				} else if (type === 'new') {
					state.new = false;
				}
				triggerFilterChange(true);
				return;
			}

			// Clear all / Reset buttons
			if (e.target.closest('[data-clear]')) {
				e.preventDefault();
				clearAllFilters();
				return;
			}

			// Pagination clicks (event delegation)
			const pageLink = e.target.closest('.shop-pagination a, .shop-pagination button[data-page]');
			if (pageLink && !pageLink.disabled) {
				e.preventDefault();
				const pageNum = parseInt(pageLink.dataset.page, 10);
				if (pageNum && pageNum !== state.paged) {
					state.paged = pageNum;
					triggerFilterChange(false, true, true);
				}
				return;
			}

			// Add to bag [data-add] on product cards
			const addBtn = e.target.closest('.add-product[data-add], .quick-add[data-add]');
			if (addBtn) {
				e.preventDefault();
				handleQuickAdd(addBtn);
				return;
			}
		});

		// Mobile Filter Dialog
		const mobileMQ = window.matchMedia('(max-width: 900px)');
		function syncFilterPanelPlacement() {
			if (!sidebarPanel) return;
			if (mobileMQ.matches) {
				if (mobileFilterContent && sidebarPanel.parentElement !== mobileFilterContent) {
					mobileFilterContent.appendChild(sidebarPanel);
				}
			} else {
				const desktopSidebar = document.querySelector('.shop-sidebar');
				if (desktopSidebar && sidebarPanel.parentElement !== desktopSidebar) {
					desktopSidebar.appendChild(sidebarPanel);
				}
				if (filterDialog?.open) {
					filterDialog.close();
					document.body.classList.remove('is-locked');
				}
			}
		}

		syncFilterPanelPlacement();
		mobileMQ.addEventListener('change', syncFilterPanelPlacement);

		filterOpenBtn?.addEventListener('click', () => {
			if (filterDialog && !filterDialog.open) {
				filterDialog.showModal();
				document.body.classList.add('is-locked');
			}
		});

		const filterApplyBtn = document.getElementById('filter-apply');
		filterApplyBtn?.addEventListener('click', () => {
			if (filterDialog?.open) {
				filterDialog.close();
				document.body.classList.remove('is-locked');
			}
		});

		filterDialog?.querySelectorAll('.close-dialog').forEach(btn => {
			btn.addEventListener('click', () => {
				filterDialog.close();
				document.body.classList.remove('is-locked');
			});
		});

		filterDialog?.addEventListener('click', e => {
			if (e.target === filterDialog) {
				const rect = filterDialog.getBoundingClientRect();
				if (e.clientX < rect.left || e.clientX > rect.right || e.clientY < rect.top || e.clientY > rect.bottom) {
					filterDialog.close();
					document.body.classList.remove('is-locked');
				}
			}
		});

		filterDialog?.addEventListener('close', () => { document.body.classList.remove('is-locked'); filterOpenBtn?.focus(); });

		filterDialog?.addEventListener('cancel', () => {
			document.body.classList.remove('is-locked');
		});

		// Popstate for Browser Back/Forward navigation
		window.addEventListener('popstate', () => {
			parseURLState();
			fetchFilteredCatalogue(false);
		});
	}

	// Add to Bag action for simple in-stock products
	async function handleQuickAdd(button) {
		const productId = button.dataset.add;
		if (!productId || button.dataset.busy) return;

		button.dataset.busy = '1';
		const originalText = button.innerHTML;

		const formData = new FormData();
		formData.append('action', 'urban_shisha_add_to_cart');
		formData.append('nonce', cartNonce);
		formData.append('product_id', productId);
		formData.append('quantity', '1');

		try {
			const res = await fetch(ajaxUrl, {
				method: 'POST',
				body: formData,
				credentials: 'same-origin',
			});

			const data = await res.json();
			if (res.ok && data && (data.success || data.fragments)) {
				if (window.UrbanCommerce?.showToast) {
					window.UrbanCommerce.showToast('Added to your bag.');
				}

				button.classList.add('is-added');
				button.innerHTML = `<svg aria-hidden="true" width="18" height="18"><use href="#i-check"/></svg>`;
				setTimeout(() => {
					button.innerHTML = originalText;
					button.classList.remove('is-added');
				}, 1400);

                const fragments = data.fragments || data.data?.fragments;
                if(fragments && window.jQuery) Object.entries(fragments).forEach(([selector, html]) => window.jQuery(selector).replaceWith(html));
                window.UrbanCommerce?.openDrawer('cart');

				// Trigger fragment refresh
				if (window.jQuery && typeof window.jQuery(document.body).trigger === 'function') {
					window.jQuery(document.body).trigger('wc_fragment_refresh');
				}
			} else {
				const msg = data?.data?.message || 'Item unavailable. View product page for details.';
				if (window.UrbanCommerce?.showToast) {
					window.UrbanCommerce.showToast(msg);
				}
			}
		} catch (err) {
			if (window.UrbanCommerce?.showToast) {
				window.UrbanCommerce.showToast('Could not add to bag. Please try again.');
			}
		} finally {
			delete button.dataset.busy;
		}
	}

	// Initialize on page load
	document.addEventListener('DOMContentLoaded', () => {
		parseURLState();
		initListeners();
	});
})();
