<?php 

class UniversityRepresentative
{
    use Model;
    protected $table = 'university_representatives';

    public function isRep($userId){
        $result = $this->first(['id' => $userId]);
        return $result ? true : false;
    }
    
    public function getRepDetails($userId){
        return $this->first(['id' => $userId]);
    }
}