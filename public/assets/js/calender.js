let allItems = [];

function parseToItem(data, type){
	const dateObj = new Date(data.timestamp); 

	// Get date in YYYY-MM-DD format
	const date = dateObj.toISOString().slice(0, 10);

	// Get time in HH:MM:SS format (in UTC)
	const time = dateObj.toISOString().slice(11, 19);

	return {
		type: type,
		title: data.title,
		date: date,
		time: time,
		is_favorite: data.is_favorite ? true : false,
		is_participating: data.is_participating ? true : false,
	};
}

function renderCalendarItem(item) {
	const card = document.createElement('div');
	card.className = 'calendar-card';
	
	const typeClass = item.type === 'event' ? 'event' : 'kuppi';
	
	card.innerHTML = `
		<div class="calendar-card-content">
			<div class="calendar-card-left">
				<div class="calendar-card-header-inline">
					<h3 class="calendar-card-title">${item.title}</h3>
					<span class="calendar-card-type ${typeClass}">${item.type}</span>
				</div>
				
				<div class="calendar-card-datetime">
					<span class="datetime-label">
						<i data-lucide="calendar" class="datetime-icon"></i>
						${item.date}
					</span>
					<span class="datetime-label">
						<i data-lucide="clock" class="datetime-icon"></i>
						${item.time}
					</span>
				</div>
			</div>
			
			<div class="calendar-card-right">
				<div class="calendar-card-status">
					${item.is_favorite ? `
						<div class="status-item favorite">
							<i data-lucide="heart" class="icon-small"></i>
							<span>Favorited</span>
						</div>
					` : ''}
					
					${item.is_participating ? `
						<div class="status-item participating">
							<i data-lucide="users" class="icon-small"></i>
							<span>Participating</span>
						</div>
					` : ''}
				</div>
			</div>
		</div>
	`;
	
	return card;
}

function renderItems(items) {
	const container = document.getElementById('calender-container');
	container.innerHTML = '';
	
	if (items.length === 0) {
		document.getElementById('noItemMsg').classList.remove('hidden');
		return;
	}
	
	document.getElementById('noItemMsg').classList.add('hidden');
	
	items.forEach(item => {
		container.appendChild(renderCalendarItem(item));
	});
	
	// Initialize Lucide icons after rendering
	lucide.createIcons();
}

function filterItemsByDateRange(startDate, endDate) {
	if (!startDate && !endDate) {
		return allItems;
	}
	
	return allItems.filter(item => {
		const itemDate = new Date(item.date);
		
		if (startDate) {
			const start = new Date(startDate);
			if (itemDate < start) return false;
		}
		
		if (endDate) {
			const end = new Date(endDate);
			if (itemDate > end) return false;
		}
		
		return true;
	});
}

function setupDateFilters() {
	const startDateInput = document.getElementById('startDate');
	const endDateInput = document.getElementById('endDate');
	const filterBtn = document.getElementById('filterBtn');
	const clearBtn = document.getElementById('clearBtn');
	
	function applyFilter() {
		const startDate = startDateInput.value;
		const endDate = endDateInput.value;
		
		if (startDate && endDate && new Date(startDate) > new Date(endDate)) {
			alert('Start date must be before end date');
			return;
		}
		
		const filteredItems = filterItemsByDateRange(startDate, endDate);
		renderItems(filteredItems);
	}
	
	filterBtn.addEventListener('click', applyFilter);
	
	clearBtn.addEventListener('click', function() {
		startDateInput.value = '';
		endDateInput.value = '';
		renderItems(allItems);
	});
	
	// Optional: Auto-filter on date change
	startDateInput.addEventListener('change', applyFilter);
	endDateInput.addEventListener('change', applyFilter);
}

document.addEventListener('DOMContentLoaded', async function () {

	try {
		const responseData = await Ajax.jsonPost('/calendar/getData');

		const events = responseData.events || [];
		const kuppi = responseData.kuppi || [];

		// Store all items for filtering
		allItems = [];
		events.forEach(event => {allItems.push(parseToItem(event, 'event'));});
		kuppi.forEach(kuppi => {allItems.push(parseToItem(kuppi, 'kuppi'));});

		// Render all items initially
		renderItems(allItems);
		
		// Setup date filter listeners
		setupDateFilters();


	} catch (error) {
		console.error("Failed to load calendar data:", error);
	}
});
