<?php

class University
{
    use Model;
    protected $table = 'universities';
    //protected $allowedColumns = ['name', 'age'];

    public function getUniversityName($id){
        $result = $this->where(conditions: [['id','=' ,$id]], limit: 1);
        error_log("University lookup for ID $id returned: " . print_r($result, true));
        return $result ? $result[0]->name : null;
    }
}