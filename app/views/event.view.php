<h1>Testing Event Page</h1>

<h2>Create a New Event</h2>

<form method="POST" action="/event/createNewEvent">
    <label>Title: <input type="text" name="title" maxlength="255" required></label><br>
    <label>Description:<br>
        <textarea name="description" required></textarea>
    </label><br>
    <label>Event Date: <input type="datetime-local" name="event_timestamp"></label><br>
    <label>Location: <input type="text" name="held_at" maxlength="255"></label><br>
    <button type="submit">Create Event</button>
</form>
<hr>

<h2>Your Upcoming Events</h2>

<?php foreach ($data['events'] as $event): ?>
    <div>
        <h2><?php print_r($event); ?></h2>
    </div>
<?php endforeach; ?>

<hr>

<h2>Your university upcoming events</h2>

<?php foreach ($data['university_events'] as $event): ?>
    <div>
        <h2><?php print_r($event); ?></h2>
    </div>
<?php endforeach; ?>

<?php var_dump($data['university_events']); ?>

<hr>

<h2>Update an Event</h2>

<?php foreach ($data['university_events'] as $event): ?>

    <form method="POST" action="/event/updateEvent" >
        <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($event->id); ?>">
        <label>Title: <input type="text" name="title" maxlength="255" required value="<?php echo htmlspecialchars($event->title); ?>"></label><br>
        <label>Description:<br>
            <textarea name="description" required><?php echo htmlspecialchars($event->description); ?></textarea>
        </label><br>
        <label>Event Date: <input type="datetime-local" name="event_timestamp" value="<?php echo isset($event->event_timestamp) ? date('Y-m-d\TH:i', strtotime($event->event_timestamp)) : ''; ?>"></label><br>
        <label>Location: <input type="text" name="held_at" maxlength="255" value="<?php echo htmlspecialchars($event->held_at); ?>"></label><br>
        <button type="submit">Update Event</button>
    </form>
<?php endforeach; ?>


<hr>
<h2>Delete an Event</h2>

<?php foreach ($data['university_events'] as $event): ?>

    <form method="POST" action="/event/deleteEvent" >
        <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($event->id); ?>">
        <p>Are you sure you want to delete the event: <strong><?php echo htmlspecialchars($event->title); ?></strong>?</p>
        <button type="submit">Delete Event</button>
    </form>
<?php endforeach; ?>