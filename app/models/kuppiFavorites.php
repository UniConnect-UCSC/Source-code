<?php

class KuppiFavoriteModel {
    use Model;

    protected $table = 'kuppi_favorites';

    private function isFavorite($kuppiId, $userId) {
        return $this->first([
            'user_id' => $userId,
            'kuppi_id' => $kuppiId
        ]);
    }

    public function markFavorite($kuppiId, $userId) {
        if ($this->isFavorite($kuppiId, $userId)) {
            return null;
        }

        $this->insert(
            ['user_id', 'kuppi_id'],
            [[$userId, $kuppiId]]
        );

        return true;
    }

    public function unmarkFavorite($kuppiId, $userId) {
        if (!$this->isFavorite($kuppiId, $userId)) {
            return null;
        }

        $this->delete([
            ['user_id', '=', $userId],
            ['kuppi_id', '=', $kuppiId]
        ], false);

        return true;
    }

    public function getUsersForKuppi($kuppiId) {
        $favorites = $this->where([['kuppi_id', '=', $kuppiId]]);
        return $favorites ? $favorites : [];
    }
}