/**
 * Category archive grid/list view toggle.
 *
 * @package Maglist_Child
 */
(function () {
	'use strict';

	var STORAGE_KEY = 'na-cat-view';

	function applyView(feed, buttons, view) {
		var list = feed.querySelector('.na-cat__feed-list');
		var grid = feed.querySelector('.na-cat__feed-grid');
		if (!list || !grid) {
			return;
		}

		var isGrid = view === 'grid';
		feed.classList.toggle('na-cat__feed--grid', isGrid);
		feed.classList.toggle('na-cat__feed--list', !isGrid);
		list.hidden = isGrid;
		grid.hidden = !isGrid;

		buttons.forEach(function (btn) {
			var active = btn.getAttribute('data-na-cat-view') === view;
			btn.classList.toggle('is-active', active);
			btn.setAttribute('aria-pressed', active ? 'true' : 'false');
		});

		try {
			window.localStorage.setItem(STORAGE_KEY, view);
		} catch (e) {
			/* ignore */
		}
	}

	function init() {
		var root = document.querySelector('.na-cat');
		if (!root) {
			return;
		}

		var feed = root.querySelector('[data-na-cat-feed]');
		var buttons = Array.prototype.slice.call(
			root.querySelectorAll('[data-na-cat-view]')
		);
		if (!feed || !buttons.length) {
			return;
		}

		var saved = 'list';
		try {
			saved = window.localStorage.getItem(STORAGE_KEY) || 'list';
		} catch (e) {
			saved = 'list';
		}
		if (saved !== 'grid' && saved !== 'list') {
			saved = 'list';
		}

		applyView(feed, buttons, saved);

		buttons.forEach(function (btn) {
			btn.addEventListener('click', function () {
				applyView(feed, buttons, btn.getAttribute('data-na-cat-view'));
			});
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
