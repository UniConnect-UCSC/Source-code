<?php

class SMVolunteerModel {
    use Model;
    protected $table = 'study_material_volunteers';

    public function createVolunteer($userId) {
        return $this->insert(['id'], [$userId]);
    }

    public function isVolunteer($userId) {
        return $this->first(['id' => $userId]) ? true : false;
    }
}