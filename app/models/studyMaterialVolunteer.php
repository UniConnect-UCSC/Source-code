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

    // This requires pre validation if the user is already a volunteer
    public function incrementSMCount($userId, $decrement = false) {
        $amount = $decrement ? -1 : 1;

        $return = $this->increment(
            id: $userId,
            column: 'total_material_count',
            amount: $amount,
            id_column: 'id'
        );

        return $return ? true : false;
    }
}