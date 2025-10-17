<?php 
class KuppiModel {
    use Model;
    protected $table = 'kuppi';

    public function getKuppi(){
        return $this->findAll();
    }
    public function getMyKuppies($user_id){
        $result = $this->where(conditions: [['host_id','=',$user_id]],limit: 10 );
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
}
