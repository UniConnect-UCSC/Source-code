<?php

class EventFavoritesModel{
    use Model;
    protected $table = 'event_favorites';
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