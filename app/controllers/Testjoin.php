<?php

require_once (__DIR__ . "/../core/functions.php");

class TestJoin extends Controller
{
    public function index()
    {
        require_once(__DIR__ . "/../models/Event.php");
        $eventModel = new EventModel();

        $join = [
            ['universities', 'm.university_id = u.id', 'INNER', 'u']
        ];

        $conditions = [
            ['m.name', 'ILIKE', '%a%'],
        ];

        $events = $eventModel->where($conditions, limit: 10, offset: 0, orderBy: ['m.event_timestamp' => 'ASC'], join: $join);

        foreach ($events as $event) {
            echo "Event: " . $event->title . " | University: " . $event->university_name . "<br>";
        }

        return;
    }
}