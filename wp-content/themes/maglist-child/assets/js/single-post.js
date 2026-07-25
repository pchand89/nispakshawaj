/**
 * Single post: copy link + DB-backed reactions.
 *
 * @package Maglist_Child
 */
(function () {
	'use strict';

	function initCopy() {
		var buttons = document.querySelectorAll('[data-na-copy-link]');
		buttons.forEach(function (btn) {
			btn.addEventListener('click', function () {
				var url = btn.getAttribute('data-na-copy-link') || '';
				if (!url) {
					return;
				}

				var done = function () {
					btn.classList.add('is-copied');
					btn.setAttribute('title', 'कपी भयो');
					window.setTimeout(function () {
						btn.classList.remove('is-copied');
						btn.setAttribute('title', 'लिंक कपी');
					}, 1600);
				};

				if (navigator.clipboard && navigator.clipboard.writeText) {
					navigator.clipboard.writeText(url).then(done).catch(function () {
						fallbackCopy(url, done);
					});
				} else {
					fallbackCopy(url, done);
				}
			});
		});
	}

	function fallbackCopy(url, done) {
		var input = document.createElement('input');
		input.value = url;
		input.setAttribute('readonly', '');
		input.style.position = 'absolute';
		input.style.left = '-9999px';
		document.body.appendChild(input);
		input.select();
		try {
			document.execCommand('copy');
			done();
		} catch (e) {
			/* ignore */
		}
		document.body.removeChild(input);
	}

	function storageKey(postId) {
		return 'na-single-reaction-selected-' + postId;
	}

	function readSelected(postId) {
		try {
			return window.localStorage.getItem(storageKey(postId)) || '';
		} catch (e) {
			return '';
		}
	}

	function writeSelected(postId, selected) {
		try {
			if (selected) {
				window.localStorage.setItem(storageKey(postId), selected);
			} else {
				window.localStorage.removeItem(storageKey(postId));
			}
		} catch (e) {
			/* ignore */
		}
	}

	function renderReactions(root, counts, selected) {
		root.querySelectorAll('[data-na-reaction]').forEach(function (btn) {
			var key = btn.getAttribute('data-na-reaction');
			var countEl = btn.querySelector('[data-na-reaction-count]');
			var count = counts && counts[key] ? parseInt(counts[key], 10) || 0 : 0;
			if (countEl) {
				countEl.textContent = String(count);
			}
			var active = selected === key;
			btn.classList.toggle('is-active', active);
			btn.setAttribute('aria-pressed', active ? 'true' : 'false');
		});
		root.setAttribute('data-na-selected', selected || '');
	}

	function getConfig() {
		return window.naSinglePost || {};
	}

	function postReaction(postId, reaction) {
		var cfg = getConfig();
		var url = (cfg.restUrl || '').replace(/\/?$/, '/') + 'reactions/' + encodeURIComponent(postId);
		return fetch(url, {
			method: 'POST',
			credentials: 'same-origin',
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': cfg.restNonce || '',
			},
			body: JSON.stringify({ reaction: reaction || '' }),
		}).then(function (res) {
			return res.json().then(function (body) {
				if (!res.ok) {
					var msg =
						(body && body.message) ||
						(cfg.i18n && cfg.i18n.error) ||
						'Error';
					throw new Error(msg);
				}
				return body;
			});
		});
	}

	function initReactions() {
		var root = document.querySelector('[data-na-reactions]');
		if (!root) {
			return;
		}

		var postId = root.getAttribute('data-na-post-id') || '';
		if (!postId) {
			return;
		}

		var cfg = getConfig();
		var counts = cfg.counts && typeof cfg.counts === 'object' ? cfg.counts : {};
		var selected =
			root.getAttribute('data-na-selected') ||
			(cfg.selected || '') ||
			readSelected(postId) ||
			'';

		renderReactions(root, counts, selected);
		writeSelected(postId, selected);

		var busy = false;

		root.querySelectorAll('[data-na-reaction]').forEach(function (btn) {
			btn.addEventListener('click', function () {
				if (busy) {
					return;
				}

				var key = btn.getAttribute('data-na-reaction');
				if (!key) {
					return;
				}

				var next = selected === key ? '' : key;
				var prevSelected = selected;
				var prevCounts = Object.assign({}, counts);

				// Optimistic UI.
				var optimistic = Object.assign({}, counts);
				if (prevSelected && optimistic[prevSelected]) {
					optimistic[prevSelected] = Math.max(
						0,
						(parseInt(optimistic[prevSelected], 10) || 0) - 1
					);
				}
				if (next) {
					optimistic[next] = (parseInt(optimistic[next], 10) || 0) + 1;
				}
				selected = next;
				counts = optimistic;
				writeSelected(postId, selected);
				renderReactions(root, counts, selected);
				root.classList.add('is-loading');
				busy = true;

				postReaction(postId, next)
					.then(function (body) {
						counts = body.counts || counts;
						selected = typeof body.selected === 'string' ? body.selected : selected;
						writeSelected(postId, selected);
						renderReactions(root, counts, selected);
					})
					.catch(function () {
						counts = prevCounts;
						selected = prevSelected;
						writeSelected(postId, selected);
						renderReactions(root, counts, selected);
						root.classList.add('is-error');
						window.setTimeout(function () {
							root.classList.remove('is-error');
						}, 1800);
					})
					.finally(function () {
						busy = false;
						root.classList.remove('is-loading');
					});
			});
		});
	}

	function init() {
		initCopy();
		initReactions();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
