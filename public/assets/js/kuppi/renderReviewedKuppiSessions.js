(function () {
	function el(tag, className, text) {
		var node = document.createElement(tag);
		if (className) node.className = className;
		if (typeof text === 'string') node.textContent = text;
		return node;
	}

	function formatDate(raw) {
		if (!raw) return '-';
		var parsed = new Date(String(raw).replace(' ', 'T'));
		if (isNaN(parsed.getTime())) {
			return String(raw).split(' ')[0] || '-';
		}
		var y = parsed.getFullYear();
		var m = String(parsed.getMonth() + 1).padStart(2, '0');
		var d = String(parsed.getDate()).padStart(2, '0');
		return y + '-' + m + '-' + d;
	}

	function clampRating(value) {
		var number = Number(value);
		if (!isFinite(number)) return 0;
		if (number < 0) return 0;
		if (number > 5) return 5;
		return number;
	}

	function buildRatingCell(value) {
		var rating = clampRating(value);
		var rounded = Math.round(rating);
		var stars = '';
		for (var i = 0; i < 5; i++) {
			stars += i < rounded ? '★' : '☆';
		}

		var wrapper = el('div', 'reviewed-kuppi-rating');
		wrapper.appendChild(el('span', 'reviewed-kuppi-rating-stars', stars));
		wrapper.appendChild(el('span', 'reviewed-kuppi-rating-value', rating ? rating.toFixed(1) : '0.0'));
		return wrapper;
	}

	function buildHeader() {
		var header = el('div', 'reviewed-kuppi-header');
		header.appendChild(el('div', '', 'Title'));
		header.appendChild(el('div', '', 'Date'));
		header.appendChild(el('div', '', 'Rating'));
		header.appendChild(el('div', '', 'Review Text'));
		header.appendChild(el('div', 'text-right', 'Actions'));
		return header;
	}

	function refreshReviewedList() {
		var scroll = window.newReviewedKuppiScroll;
		if (!scroll) return;
		scroll.resetScroll();
		scroll.loadNextElements();
	}

	function deleteReview(item) {
		var reviewId = item && item.review_id ? String(item.review_id) : '';
		if (!reviewId) return;

		if (!window.confirm('Delete this review? This action cannot be undone.')) return;

		Ajax.jsonPost('/kuppi/deleteReview', {
			review_id: reviewId,
			host_id: item && item.host_id ? String(item.host_id) : ''
		}).then(function (response) {
			if (response && response.success) {
				refreshReviewedList();
				return;
			}
			window.alert((response && response.message) || 'Failed to delete review.');
		}).catch(function () {
			window.alert('Failed to delete review.');
		});
	}

	function makeActionButton(title, iconName, onClick, extraClass) {
		var btn = el('button', 'icon-btn reviewed-action-btn ' + (extraClass || ''));
		btn.type = 'button';
		btn.title = title;
		btn.setAttribute('aria-label', title);
		btn.innerHTML = '<i data-lucide="' + iconName + '"></i>';
		btn.onclick = function (e) {
			e.stopPropagation();
			onClick();
		};
		return btn;
	}

	function buildRow(item) {
		var row = el('div', 'reviewed-kuppi-row');

		var title = el('div', 'reviewed-kuppi-cell reviewed-kuppi-cell-title', item.topic || 'Untitled Kuppi');
		var date = el('div', 'reviewed-kuppi-cell', formatDate(item.kuppi_date_time));

		var ratingCell = el('div', 'reviewed-kuppi-cell');
		ratingCell.appendChild(buildRatingCell(item.rating));

		var reviewText = el('div', 'reviewed-kuppi-cell reviewed-kuppi-cell-review', item.review_text || '-');

		var actions = el('div', 'reviewed-kuppi-cell reviewed-kuppi-actions');
		actions.appendChild(makeActionButton('View review', 'eye', function () {
			if (typeof window.openReviewModal === 'function') {
				window.openReviewModal({
					mode: 'view',
					title: item.topic || 'Kuppi Review',
					rating: Number(item.rating || 0),
					comment: item.review_text || '',
					date: formatDate(item.kuppi_date_time)
				});
			}
		}));

		actions.appendChild(makeActionButton('Edit review', 'pencil', function () {
			if (typeof window.openReviewModal === 'function') {
				window.openReviewModal({
					mode: 'edit',
					review_id: item && item.review_id ? String(item.review_id) : '',
					kuppi_id: item && item.kuppi_id ? String(item.kuppi_id) : (item && item.id ? String(item.id) : ''),
					host_id: item && item.host_id ? String(item.host_id) : '',
					rating: Number(item.rating || 0),
					comment: item.review_text || ''
				});
			}
		}, 'edit-btn'));

		actions.appendChild(makeActionButton('Delete review', 'trash-2', function () {
			deleteReview(item);
		}, 'delete-btn'));

		row.appendChild(title);
		row.appendChild(date);
		row.appendChild(ratingCell);
		row.appendChild(reviewText);
		row.appendChild(actions);

		return row;
	}

	function renderReviewedKuppiCards(item) {
		var container = document.getElementById('my-kuppi-content');
		if (container) {
			container.classList.add('reviewed-table-list');
		}

		var fragment = document.createDocumentFragment();
		if (container && !container.querySelector('.reviewed-kuppi-header')) {
			fragment.appendChild(buildHeader());
		}
		fragment.appendChild(buildRow(item || {}));
		return fragment;
	}

	window.renderReviewedKuppiCards = renderReviewedKuppiCards;
})();