<?php

class User
{
    use Model;
    protected $table = 'users';
    //protected $allowedColumns = ['name', 'age'];

    public function getUniId($user_id){
        $user = $this->where(conditions: [['id','=',$user_id]], limit: 1);
        return $user ? $user[0]->university_id : null;
    }
    public function getUserNameById($user_id){
        $user = $this->where(conditions: [['id','=',$user_id]], limit: 1);
        return $user ? $user[0]->f_name . ' ' . $user[0]->l_name  : null;
    }
    public function getUserUniversityById($user_id) {
        $user = $this->where([['id', '=', $user_id]], 1);
        return $user && isset($user[0]->university_id) ? $user[0]->university_id : null;
    }
    public function getUserUniversityNameById($user_id) {
        $user = $this->where([['id', '=', $user_id]], 1);
        if ($user && isset($user[0]->university_id)) {
            $universityModel = new University();
            return $universityModel->getUniversityName($user[0]->university_id);
        }
        return null;
    }
}