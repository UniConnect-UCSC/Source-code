<?php
require_once(__DIR__ . "/../../models/Event.php");
require_once(__DIR__ . "/../../models/University.php");

$eventModel = new EventModel();
$universityModel = new University();
$latestEvents = $eventModel->getMostRecentEvents(5);

?>

<div class="events-widget">
    <div>
        <p>Upcoming Events</p>
    </div>

    <?php foreach ($latestEvents as $event):
        $title = $event->title;
        $universityName = $event->university_name;
        $eventDate = new DateTime($event->event_timestamp);
        $eventDay = $eventDate->format('d');
        $eventMonth = $eventDate->format('M');

    ?>

    <a class="event" href="">
        <div class="event-date">
            <span><?php echo $eventDay; ?></span>
            <span><?php echo $eventMonth; ?></span>
        </div>
        <div class="event-info">
            <p><?php echo $title; ?></p>
            <span><?php echo $universityName; ?></span>
        </div>
    </a>

    <?php endforeach; ?>

    <div class="view-all-events">
        <a href="/events">See all Events</a>
    </div>
</div>