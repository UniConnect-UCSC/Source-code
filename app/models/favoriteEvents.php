<?php

class FavoriteEventsModel{
    use Model;
    protected $table = 'favorite_events';
    protected $columns = ['user_id', 'event_id'];

    function addFavorite($userId, $eventId){

        $this->insert($this->columns,[[$userId, $eventId]]);
    }

    function removeFavorite($userId, $eventId){
        $this->delete([
            ['user_id', '=', $userId],
            ['event_id', '=', $eventId]
        ]);
    }

}