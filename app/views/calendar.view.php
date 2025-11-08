<?php component("navbar"); ?>

<div class="home-layout">
	<?php component("navPanel"); ?>

	<div class="feed">
		<div style="padding: var(--spacing-6);">
			<h2 class="feed-title">Calendar</h2>
			<?php component("calender", ["bookmarkedDates" => $bookmarkedDates ?? []]); ?>
		</div>
	</div>

	<?php component("widgetPanel"); ?>
</div>
