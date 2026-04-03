<?php
/**
 * IMPORTANT!!!!!!
 * maintaining Consistency of the total counts of participants for events in the event table should be handled in this model
 * Otherwise, the counts may become inconsistent with actual participations
 */

class EventParticipationModel {
    use Model;
    protected $table = 'event_participations';

    private function isParticipating($userId, $eventId) {
        //$this->first(['user_id' => $userId, 'event_id' => $eventId]);
        error_log("Before isParticipating: userId=$userId, eventId=$eventId");
        $participation = $this->first([
            'user_id' => $userId,
            'event_id' => $eventId
        ]);
        
        error_log("After isParticipating: ");
        error_log("EventParticipation: " .print_r($participation, true));

        return $participation;

    }    

    public function addParticipation($userId, $eventId) {
        if ($this->isParticipating($userId, $eventId)) {
            return null;
        }

        $this->insert(
            ['user_id', 'event_id'],
            [[$userId, $eventId]]
        );

        return $this->updateAndFetchParticipationCount($eventId, true);
    }

    public function removeParticipation($userId, $eventId) {

        if (!$this->isParticipating($userId, $eventId)) {
            return null;
        }
    
        // Delete set to false for permanent deletion
        $this->delete([
            ['user_id', '=', $userId],
            ['event_id', '=', $eventId]
        ],
        false
    );

        return $this->updateAndFetchParticipationCount($eventId, false);
    }

    
    // This function is only implemented for deletion when event is deleted.
    // Thus a count update is not needed
    public function removeAllParticipatorsForEvent($eventId) {
        $this->delete([
            ['event_id', '=', $eventId]
        ]);
        return true;
    } 

    public function getParticipatorsForEvent($eventId) {
        $participators = $this->where([['event_id', '=', $eventId]]);
        return $participators ? $participators : [];
    }

    private function updateAndFetchParticipationCount($eventId, $increment = true) {
            require_once(__DIR__ . "/Event.php");
            $eventModel = new EventModel();
            $eventModel->incrementParticipantCount($eventId, $increment);
            return $eventModel->getParticipantCount($eventId);
    }

}