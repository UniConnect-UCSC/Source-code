document.addEventListener('DOMContentLoaded', async function () {
	const monthYearEl = document.getElementById('calendarMonthYear');
	const tbody = document.getElementById('calendarBody');
	const prevBtn = document.getElementById('prevMonthBtn');
	const nextBtn = document.getElementById('nextMonthBtn');

	if (!monthYearEl || !tbody || !prevBtn || !nextBtn) return;

	let view = new Date();
	view.setDate(1);

	let itemsByDate = {};

	function pad(n) { return n.toString().padStart(2, '0'); }
	function fmt(date) { return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`; }

	prevBtn.addEventListener('click', () => {
		view.setMonth(view.getMonth() - 1);
		render();
	});
	nextBtn.addEventListener('click', () => {
		view.setMonth(view.getMonth() + 1);
		render();
	});

	function render() {
		const year = view.getFullYear();
		const month = view.getMonth();
		const firstDayWeekIdx = new Date(year, month, 1).getDay(); // 0 = Sun
		const daysInMonth = new Date(year, month + 1, 0).getDate();

		const monthName = view.toLocaleString(undefined, { month: 'long', year: 'numeric' });
		monthYearEl.textContent = monthName;

		tbody.innerHTML = '';

		let cell = 0;
		let day = 1;
		const todayStr = fmt(new Date());

		for (let row = 0; row < 6; row++) {
			const tr = document.createElement('tr');
			for (let col = 0; col < 7; col++, cell++) {
				const td = document.createElement('td');
				if (cell >= firstDayWeekIdx && day <= daysInMonth) {
					const dateObj = new Date(year, month, day);
					const dateStr = fmt(dateObj);
					td.classList.add('calendar-day');

					const header = document.createElement('div');
					header.className = 'day-header';
					header.textContent = String(day);
					td.appendChild(header);

					if (todayStr === dateStr) td.classList.add('today');

					// Render actual items for the day, if any
					const dayItems = itemsByDate[dateStr] || [];
					if (dayItems.length) {
						const list = document.createElement('div');
						list.className = 'day-items';

						// Show up to 3 items per day to keep cell compact
						dayItems.slice(0, 3).forEach((it) => {
							const row = document.createElement('div');
							const typeClass = it.type === 'kuppi' ? 'item--kuppi' : 'item--event';
							row.className = `item ${typeClass}`;

							const topRow = document.createElement('div');
							topRow.className = 'item-top-row';

							const timeDiv = document.createElement('div');
							timeDiv.textContent = it.time || '';

							const iconsWrapper = document.createElement('div');
							iconsWrapper.className = 'item-icons';

							if (it.is_favorite) {
								const heartIcon = document.createElement('i');
								heartIcon.setAttribute('data-lucide', 'heart');
								iconsWrapper.appendChild(heartIcon);
							}
							if (it.is_participating) {
								const checkIcon = document.createElement('i');
								checkIcon.setAttribute('data-lucide', 'users');
								iconsWrapper.appendChild(checkIcon);
							}

							topRow.appendChild(timeDiv);
							topRow.appendChild(iconsWrapper);

							const titleDiv = document.createElement('div');
							titleDiv.className = 'item-title';
							titleDiv.textContent = it.title;

							if (it.time || it.is_favorite || it.is_participating) {
								row.appendChild(topRow);
							}
							row.appendChild(titleDiv);
							list.appendChild(row);
						});

						if (dayItems.length > 3) {
							const more = document.createElement('div');
							more.className = 'item item--more';
							more.textContent = `+${dayItems.length - 3} more`;
							list.appendChild(more);
						}

						td.appendChild(list);
					}

					day++;
				} else {
					td.classList.add('calendar-day--empty');
					td.innerHTML = '&nbsp;';
				}
				tr.appendChild(td);
			}
			tbody.appendChild(tr);
			if (day > daysInMonth) break; // don't render trailing empty rows
		}

		if (window.lucide && typeof window.lucide.createIcons === 'function') {
			window.lucide.createIcons();
		}
	}

	// Initial skeleton render
	render();

	// Load data from the server
	try {
		const responseData = await Ajax.jsonPost('/calendar/getData');
		console.log("Calendar data loaded:", responseData);

		const events = responseData.events || [];
		const kuppis = responseData.kuppi || [];

		const items = [];
		events.forEach(item => items.push({ ...item, type: 'event' }));
		kuppis.forEach(item => items.push({ ...item, type: 'kuppi' }));


		itemsByDate = {};

		for (const it of items) {
			if (!it) continue;

			// Parse the timestamp safely if it exists (e.g. "2026-05-01 17:00:00+05:30")
			if (it.timestamp) {
				const d = new Date(it.timestamp);
				// Check if the date is valid
				if (!isNaN(d.getTime())) {
					it.date = fmt(d);
					it.time = d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
				}
			}

			// Skip if there's no date to group by
			if (!it.date) continue;

			const key = String(it.date);
			
			// If this is the first item for this date, create an empty array first
			if (!itemsByDate[key]) {
				itemsByDate[key] = [];
			}

			// Add the item to its corresponding date array
			itemsByDate[key].push(it);
		}

		// Re-render with populated data
		render();
	} catch (error) {
		console.error("Failed to load calendar data:", error);
	}
});
