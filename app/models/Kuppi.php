<?php 
class KuppiModel {
    use Model;
    protected $table = 'kuppi';

    public function getKuppi(){
        $result = $this->where(conditions: [['status','=','In Progress']], limit: 10);
        return $result;
    }
    public function getMyKuppies($user_id){
        // Get Kuppies where user is host OR requester
        $result = $this->query(
            "SELECT * FROM {$this->table} WHERE host_id = :user_id OR requester_id = :user_id LIMIT 20",
            ['user_id' => $user_id]
        );
        error_log(print_r($result, true));
        return $result;
    }
    public function getKuppiById($id){
        $result = $this->where(conditions: [['id','=',$id]], limit: 1);
        return $result ? $result[0] : null;
    }

    public function updateKuppi($data)
    {
        $requiredFields = ['updated_at'];

        // Validate required fields
        if (!$this->validate($data, $requiredFields)) {
            return false;
        }

        $id_column = 'id';
        $id = $data[$id_column];
        unset($data[$id_column]); // Remove id from data to prevent updating it

        // Use the Model trait's update method
        return $this->update($id, $data, $id_column);
    }
    public function getKuppiRequests(){
        return $this->where(conditions: [['status','=','Requested']], limit: 10);
    }
}
