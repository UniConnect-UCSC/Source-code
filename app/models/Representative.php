<?php

class UniversityRepresentative
{
    use Model;
    protected $table = 'university_representatives';

    public function isRep($userId)
    {
        $result = $this->first(['id' => $userId]);
        return $result ? true : false;
    }

    public function getRepDetails($userId)
    {
        return $this->first(['id' => $userId]);
    }

    public function getRep($universityId)
    {
        $result = $this->first(['university_id' => $universityId]);
        return $result ? $result->id : null;
    }

    public function stepDown($userId)
    {
        return $this->delete([['id', '=', $userId]]);
    }
}
