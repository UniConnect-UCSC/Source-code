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

    private function getEvent($id){
        return $this->first(['id' => $id]);
    }

    public function getEventUni($id){
        $event = $this->getEvent($id);
        return $event ? $event->university_id : null;
    }

    public function getEventByUniversity($universityId){
        return $this->where(['university_id' => $universityId]);
    }

    public function getEvents($offset){
        
        return $this->findAll(offset: $offset);

    }

    public function createEvent($data){
        $requiredFields = ['university_id','posted_by' ,'title', 'description', 'event_timestamp', 'held_at'];

        if (!$this->validate($data, $requiredFields)) {
            error_log("Validation failed");
            return false;
        }

        return $this->insert($data);
    }

    public function getUniUpcomingEvents($universityId){
        return $this->where([['university_id','=', $universityId], ['event_timestamp', '>=', date('Y-m-d H:i:s', time())]]);
    }

    public function updateEvent($eventId, $data){
        $requiredFields = ['updated_at'];        

        if (!$this->validate($data, $requiredFields)) {
            return false;
        }

        $id_column = 'id';
        $id = $data[$id_column];
        unset($data[$id_column]); // Remove id from data to prevent updating it

        return $this->update($id, $data, $id_column);
    }

    public function deleteEvent($eventId){
        return $this->delete($eventId, 'id');
    }

}