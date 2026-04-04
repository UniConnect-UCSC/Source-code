<?php

class KuppiVolunteerModel {
    use Model;
    protected $table = 'kuppi_volunteers';

    public function getByStudentId($studentId) {
        $result = $this->first(['student_id' => $studentId]);
        return $result ?? null;
    }
}
