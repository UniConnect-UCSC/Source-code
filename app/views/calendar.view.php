<?php component("navbar"); ?>

<div class="home-layout">
	<?php component("navPanel"); ?>

	<div class="feed">
		<header>
			<h1>Schedule</h1>
			<p class="subtitle">View your favorite and participating Events and Kuppi</p>
		</header>
		
		<div class="controls">
			<div class="date-range-picker">
				<div class="date-range-inputs">
					<div class="date-input-group">
						<label for="startDate">From</label>
						<input type="date" id="startDate" class="date-input">
					</div>
					
					<div class="date-input-group">
						<label for="endDate">To</label>
						<input type="date" id="endDate" class="date-input">
					</div>
					
					<button id="filterBtn" class="filter-btn">Filter</button>
					<button id="clearBtn" class="clear-btn">Clear</button>
				</div>
			</div>


		<div id="calender-container"></div>

		</div>
		<div class="no-items-message hidden" id="noItemMsg">No items found for the selected date range.</div>
	</div>

	<?php component("widgetPanel"); ?>
</div>

<script src="/assets/js/calender.js"></script>