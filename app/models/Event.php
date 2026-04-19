<?php

class EventModel
{
    use Model;
    protected $table = 'events';

    private function validate($data, $requiredFields){
        $isValid = true;        

        foreach ($requiredFields as $field) {
            error_log("Validating field: " . $field);

            if (empty($data[$field])) {
                $isValid = false;

                error_log("Validation error Missing required field : " . $field);
                //$this->error["required_field_error"][$field] = "";
            }
        }
        return $isValid;
    }

    public function getMostRecentEvents($limit = 5){
        $join = [
            ["universities", "m.university_id = u.id", "INNER", "u"]
        ];
        $selected = [
            "m.title",
            "m.event_timestamp",
            ["u.name", "university_name"]
        ];

        $conditions = [
            ['event_timestamp', '>=', date('Y-m-d H:i:s', time())],
        ];
        return $this->where(conditions: $conditions, join: $join, selected: $selected, limit: $limit, orderBy: ['event_timestamp' => 'ASC']);
    }

    private function getEvent($id){
        return $this->first(['id' => $id]);
    }

    public function getParticipantCount($eventId){
        $event = $this->getEvent($eventId);
        return $event ? $event->participant_count : null;
    }

    public function incrementParticipantCount($eventId, $incrementType = true) {
        // Increment or decrement based on $incrementType 

        $amount = $incrementType ? 1 : -1;
        return $this->increment(id: $eventId, column: 'participant_count', amount: $amount);

    }
    public function getEventUni($id){
        $event = $this->getEvent($id);
        return $event ? $event->university_id : null;
    }

    public function getEventTitle($id){
        $event = $this->getEvent($id);
        return $event ? $event->title : "";
    }

    public function getEventByUniversity($universityId){
        return $this->where(['university_id' => $universityId]);
    }

    public function getUpcomingEvents($limit, $offset, $categories = [], $searchTerm = '', $onlyFavorites = false, $filterType = "for-you"){

        $conditions = [
            ['event_timestamp', '>=', date('Y-m-d H:i:s', time())],
        ];

        if(!empty($searchTerm)){
            $conditions[] = ['title', 'ILIKE', '%' . $searchTerm . '%'];
        } 

        $join = [];
        $selected = [];

        $favoritesJoinType = $onlyFavorites ? "INNER" : "LEFT";

        $join = [
            ["event_favorites", ["m.id = f.event_id", ["f.user_id", "=", $_SESSION["user_id"]]], $favoritesJoinType, "f"],
            ["event_participations", ["m.id = p.event_id", ["p.user_id", "=", $_SESSION["user_id"]]], "LEFT", "p"]
        ];

        if($filterType === "my-university"){
            $join[] = ["universities", ["m.university_id = u.id", ['u.id', '=', $_SESSION['user_universityID']]], "INNER", "u"];
        } else {
            $join[] = ["universities", "m.university_id = u.id", "INNER", "u"];
        }

        $selected = [
            "m.*",
            ["u.name", "university_name"],
            ["CASE WHEN f.event_id IS NULL THEN 0 ELSE 1 END", "is_favorite"],
            ["CASE WHEN p.event_id IS NULL THEN 0 ELSE 1 END", "is_participating"]
        ];

        // If categories provided, join the mapping table and filter by category_id (match ANY)
        $groupBy = null;
        if (!empty($categories)) {
            $join[] = ["event_category_mapping", "m.id = ecm.event_id", "INNER", "ecm"];
            $conditions[] = ['ecm.category_id', 'IN', $categories];
            $groupBy = ['m.id', 'u.name', 'is_favorite', 'is_participating']; // Group by event to avoid duplicates
        }

        $tempData = $this->where(
            conditions: $conditions,
            limit: $limit,
            offset: $offset,
            orderBy: ['event_timestamp' => 'ASC'],
            join: $join,
            selected: $selected,
            showDeleted: false,
            groupBy: $groupBy
        );

        return $tempData;
    }

    public function createEvent($data){
        $requiredFields = ['university_id','title', 'description', 'event_timestamp', 'held_at'];

        if (!$this->validate($data, $requiredFields)) {
            error_log("Validation failed");
            return false;
        }

        return $this->insertAndFetch($data)->id;
    }

    public function getUniUpcomingEvents($universityId , $limit, $offset){
        return $this->where(conditions: [['university_id','=', $universityId], ['event_timestamp', '>=', date('Y-m-d H:i:s', time())]], limit: $limit, offset: $offset, orderBy: ['event_timestamp' => 'ASC']);
    }

    public function updateEvent($eventId, $data){

        $this->update($eventId, $data, 'id');
        return true;
    }

    public function deleteEvent($eventId){
        return $this->delete([['id', '=', $eventId]], softDelete: false);
    }

    public function getEventSuggestions($limit, $offset, $searchTerm){
        $conditions = [
            ['event_timestamp', '>=', date('Y-m-d H:i:s', time())],
            ['title', 'ILIKE', '%' . $searchTerm . '%']
        ];

        $selected = [
            "m.id",
            "m.title"
        ];

        return $this->where(
            conditions: $conditions,
            limit: $limit,
            offset: $offset,
            orderBy: ['event_timestamp' => 'ASC'],
            selected: $selected,
            showDeleted: false
        );
    }

    public function getEventFavoritesAndParticipants($userId){
        $join = [
            ["event_favorites", ["m.id = f.event_id", ["f.user_id", "=", $userId]], "LEFT", "f"],
            ["event_participations", ["m.id = p.event_id", ["p.user_id", "=", $userId]], "LEFT", "p"]
        ];

        $selected = [
            "m.title",
            ["m.event_timestamp", "timestamp"],
            ["CASE WHEN f.event_id IS NULL THEN 0 ELSE 1 END", "is_favorite"],
            ["CASE WHEN p.event_id IS NULL THEN 0 ELSE 1 END", "is_participating"]
        ];

        $conditions = [
            ['f.event_id', 'IS', 'NOT NULL', 'OR'],
            ['p.event_id', 'IS', 'NOT NULL']
        ];

        return $this->where(
            conditions: $conditions,
            join: $join,
            selected: $selected,
        );
    }
}