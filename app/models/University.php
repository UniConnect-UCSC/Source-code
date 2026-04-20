<?php

class University
{
    use Model;
    protected $table = 'universities';
    //protected $allowedColumns = ['name', 'age'];
    public function getUniversityName($university_id)
    {
        $result = $this->where(conditions: [['id', '=', $university_id]], limit: 1);
        return $result ? $result[0]->name : null;
    }

    public function getTotalUniversitiees()
    {
        return (int) $this->count([]);
    }
}
