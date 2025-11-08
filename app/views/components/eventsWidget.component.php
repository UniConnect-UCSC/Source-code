<?php
<<<<<<< HEAD
require_once(__DIR__ . "/../../models/Event.php");
require_once(__DIR__ . "/../../models/University.php");

$eventModel = new EventModel();
=======
require(__DIR__ . "/../../models/Event.php");
require(__DIR__ . "/../../models/University.php");

$eventModel = new Event();
>>>>>>> main
$universityModel = new University();
$latestEvents = $eventModel->findAll();

?>

<div class="events-widget">
    <div>
        <p>Upcoming Events</p>
    </div>

    <?php foreach ($latestEvents as $event):
        $id = $event->id;
        $universityId = $event->university_id;
        $title = $event->title;
<<<<<<< HEAD
        $eventDate = new DateTime($event->event_timestamp);
        $eventDay = $eventDate->format('d');
        $eventMonth = $eventDate->format('M');
        $location = $event->held_at;
=======
        $eventDate = new DateTime($event->event_date);
        $eventDay = $eventDate->format('d');
        $eventMonth = $eventDate->format('M');
        $location = $event->location;
>>>>>>> main

        $postedBy = $event->posted_by;
        $description = $event->description;
        $createdAt = $event->created_at;
        $updatedAt = $event->updated_at;

        $university = $universityModel->first(['id' => $universityId]);
        $universityName = $university ? $university->name : 'Unknown University';
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