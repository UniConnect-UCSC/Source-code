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

    public function getReviewsWithKuppiSessions ($user_id ,$offset ,$limit) {
        $conditions = [
            ['m.reviewer_id', '=', $user_id]
        ];

        $join = [
            ['kuppi', 'm.kuppi_id = k.id', 'LEFT', 'k'],
            ['users', 'k.host_id = h.id', 'LEFT', 'h']
        ];

        $selected = [
            ['m.id', 'review_id'],
            ['m.kuppi_id', 'kuppi_id'],
            ['m.reviewer_id', 'reviewer_id'],
            ['m.review_text', 'review_text'],
            ['m.rating', 'rating'],
            ['k.id', 'id'],
            ['k.topic', 'topic'],
            ['k.kuppi_date_time', 'kuppi_date_time'],
            ['k.platform', 'platform'],
            ['k.status', 'status'],
            ['k.host_id', 'host_id'],
            ["CONCAT(h.f_name, ' ', h.l_name)", 'host_name']
        ];

        $orderBy = [
            'm.id' => 'DESC'
        ];

        $data = $this->where(
            conditions: $conditions,
            join: $join,
            selected: $selected,
            orderBy: $orderBy,
            offset: $offset,
            limit: $limit
        );

        return is_array($data) ? $data : [];
    }

    public function getReviewByIdAndReviewer($reviewId, $reviewerId) {
        $reviewId = trim((string)$reviewId);
        $reviewerId = trim((string)$reviewerId);

        if ($reviewId === '' || $reviewerId === '') {
            return null;
        }

        $results = $this->where(
            conditions: [
                ['id', '=', $reviewId],
                ['reviewer_id', '=', $reviewerId]
            ],
            limit: 1
        );

        if (!is_array($results) || empty($results)) {
            return null;
        }

        return $results[0];
    }

    public function updateReviewById($reviewId, $reviewerId, $rating, $reviewText) {
        $existing = $this->getReviewByIdAndReviewer($reviewId, $reviewerId);
        if (!$existing) {
            return false;
        }

        return $this->update($reviewId, [
            'rating' => $rating,
            'review_text' => $reviewText
        ]);
    }

    public function deleteReviewById($reviewId, $reviewerId) {
        $existing = $this->getReviewByIdAndReviewer($reviewId, $reviewerId);
        if (!$existing) {
            return false;
        }

        return $this->delete($reviewId);
    }
}