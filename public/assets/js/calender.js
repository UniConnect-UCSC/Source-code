document.addEventListener('DOMContentLoaded', function () {
	const monthYearEl = document.getElementById('calendarMonthYear');
	const tbody = document.getElementById('calendarBody');
	const prevBtn = document.getElementById('prevMonthBtn');
	const nextBtn = document.getElementById('nextMonthBtn');

	if (!monthYearEl || !tbody || !prevBtn || !nextBtn) return;

	const today = new Date();
	let view = (typeof currentDate !== 'undefined' && currentDate) ? new Date(currentDate) : new Date();

	// Normalize to first of month
	view.setDate(1);

		const bookmarked = new Set((window.bookmarkedDates || []).map(String));
		const items = Array.isArray(window.calendarItems) ? window.calendarItems : [];

		// Build quick lookup: dateStr -> items[]
		const itemsByDate = items.reduce((acc, it) => {
			if (!it || !it.date) return acc;
			const key = String(it.date);
			(acc[key] = acc[key] || []).push(it);
			return acc;
		}, {});

	prevBtn.addEventListener('click', () => {
		view.setMonth(view.getMonth() - 1);
		render();
	});
	nextBtn.addEventListener('click', () => {
		view.setMonth(view.getMonth() + 1);
		render();
	});

	function pad(n) { return n.toString().padStart(2, '0'); }
	function fmt(date) { return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`; }

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

					if (fmt(today) === dateStr) td.classList.add('today');
								// Style bookmarked dates
								if (bookmarked.has(dateStr)) td.classList.add('bookmarked');

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
										const time = it.time ? ` • ${it.time}` : '';
										row.textContent = `${it.type === 'kuppi' ? 'Kuppi' : 'Event'}: ${it.title}${time}`;
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
	}

	render();
});
