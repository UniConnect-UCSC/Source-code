<?php component("navbar"); ?>

<div class="home-layout">
	<?php component("navPanel"); ?>

	<div class="feed">
		<header>
			<h1>Calendar</h1>
			<p class="subtitle">View your favorite and participating Events and Kuppi</p>
		</header>
		<div style="padding: var(--spacing-6);">
			<script src="/assets/js/calender.js"></script>
			<div class="simple-calendar">
				<div class="simple-calendar-header">
					<button id="prevMonthBtn">&lt;</button>
					<span id="calendarMonthYear"></span>
					<button id="nextMonthBtn">&gt;</button>
				</div>
				<table class="simple-calendar-table">
					<thead>
						<tr>
							<th>Su</th><th>Mo</th><th>Tu</th><th>We</th><th>Th</th><th>Fr</th><th>Sa</th>
						</tr>   
					</thead>
					<tbody id="calendarBody"></tbody>
				</table>
			</div>
		</div>
	</div>

	<?php component("widgetPanel"); ?>
</div>
