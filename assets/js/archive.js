(function () {
	'use strict';

	var catBtns     = document.querySelectorAll('.cw-wfs-cat-filters .filter-item');
	var tagBtns     = document.querySelectorAll('.cw-wfs-tag-filters .cw-wfs-tag-btn');
	var resultsWrap = document.getElementById('cw-wfs-grid-results');

	var activeCatId = 0;
	var activeTagId = 0;

	var SCROLL_SPEED = 120; // px per second

	// ── Screenshot scroll on card hover ──────────────────────────────────────
	function getCurrentY(img) {
		var m = window.getComputedStyle(img).transform;
		if (!m || m === 'none') return 0;
		var v = m.match(/matrix\([^,]+,[^,]+,[^,]+,[^,]+,[^,]+,\s*([-\d.]+)\)/);
		return v ? parseFloat(v[1]) : 0;
	}

	function scrollTo(img, targetY) {
		var fromY = getCurrentY(img);
		var dist  = Math.abs(targetY - fromY);
		if (dist < 1) return;
		img.style.transition = 'transform ' + (dist / SCROLL_SPEED).toFixed(2) + 's linear';
		img.style.transform  = 'translateY(' + targetY + 'px)';
	}

	function initScreenScroll(root) {
		(root || document).querySelectorAll('.cw-it-screen').forEach(function (wrap) {
			if (wrap.dataset.cwScrollInit) return;
			wrap.dataset.cwScrollInit = '1';

			var img = wrap.querySelector('.cw-it-screenshot');
			if (!img) return;

			function getScrollDist() {
				var imgH = img.naturalHeight * (img.offsetWidth / (img.naturalWidth || 1));
				return Math.max(0, imgH - wrap.offsetHeight);
			}

			wrap.addEventListener('mouseenter', function () {
				var dist = getScrollDist();
				if (dist <= 0) return;
				scrollTo(img, -Math.round(dist * 0.9));
			});
			wrap.addEventListener('mouseleave', function () {
				scrollTo(img, 0);
			});
		});
	}

	initScreenScroll();

	// ── AJAX fetch ────────────────────────────────────────────────────────────
	function fetchFiltered() {
		if (!resultsWrap || typeof fetch_vars === 'undefined') return;

		resultsWrap.style.opacity       = '0.5';
		resultsWrap.style.pointerEvents = 'none';

		var filters = {};
		if (activeCatId) filters.website_category = activeCatId;
		if (activeTagId) filters.website_tag       = activeTagId;

		var body = new FormData();
		body.append('action',     'fetch_action');
		body.append('nonce',      fetch_vars.nonce);
		body.append('actionType', 'filterPosts');
		body.append('params',     JSON.stringify({ post_type: 'cw_website', template: 'cw_websites_1', filters: filters }));

		fetch(fetch_vars.ajaxurl, { method: 'POST', body: body })
			.then(function (r) { return r.json(); })
			.then(function (data) {
				if (data.status === 'success' && resultsWrap) {
					resultsWrap.innerHTML = data.data.html;
					initScreenScroll(resultsWrap);
				}
			})
			.catch(function (err) { console.error('[CW WFS] filter error:', err); })
			.finally(function () {
				if (resultsWrap) {
					resultsWrap.style.opacity       = '';
					resultsWrap.style.pointerEvents = '';
				}
			});
	}

	// ── Category filter ───────────────────────────────────────────────────────
	catBtns.forEach(function (btn) {
		btn.addEventListener('click', function (e) {
			e.preventDefault();
			activeCatId = +btn.getAttribute('data-cat-id');
			catBtns.forEach(function (b) { b.classList.remove('active'); });
			btn.classList.add('active');
			fetchFiltered();
		});
	});

	// ── Tag filter ────────────────────────────────────────────────────────────
	tagBtns.forEach(function (btn) {
		btn.addEventListener('click', function (e) {
			e.preventDefault();
			activeTagId = +btn.getAttribute('data-tag-id');
			tagBtns.forEach(function (b) {
				b.classList.remove('active', 'bg-primary', 'text-white');
				b.classList.add('bg-soft-ash', 'text-ash');
			});
			btn.classList.remove('bg-soft-ash', 'text-ash');
			btn.classList.add('active', 'bg-primary', 'text-white');
			fetchFiltered();
		});
	});

})();
