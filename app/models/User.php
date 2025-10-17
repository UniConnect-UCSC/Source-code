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
}