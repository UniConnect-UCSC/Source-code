<?php 

class KuppiVolunteerReviewModel {
    use Model;
    protected $table = 'kuppi_volunteer_reviews';

    public function getReviewsByVolunteerId($volunteerId) {
        $results = $this->where(
            conditions: [['volunteer_id', '=', $volunteerId]]
        );
        return is_array($results) ? $results : [];
    }

    /**
     * Get reviews with reviewer name for a specific kuppi
     */
    public function getReviewsByKuppiId($kuppiId) {
        if (!$kuppiId) {
            return [];
        }

        $conditions = [
            ['m.kuppi_id', '=', $kuppiId]
        ];

        $join = [
            ['users', 'm.reviewer_id = u.id', 'LEFT', 'u']
        ];

        $selected = [
            'm.*',
            ["CONCAT(u.f_name, ' ', u.l_name)", 'reviewer_name']
        ];

        $results = $this->where(
            conditions: $conditions,
            join: $join,
            selected: $selected
        );

        return is_array($results) ? $results : [];
    }
}