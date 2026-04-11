<?php

class KuppiParticipationModel {
    use Model;
    protected $table = 'kuppi_participants';


    private function isParticipating( $kuppiId , $userId) {

        $participation = $this->first([
            'user_id' => $userId,
            'kuppi_id' => $kuppiId
        ]);
        
        return $participation;
    }

    public function addParticipation ( $kuppiId , $userId ){

        if($this->isParticipating( $kuppiId , $userId)){
            return null;
        }

        $this->insert(
            ['user_id', 'kuppi_id'],
            [[$userId, $kuppiId]]
        );

        return $this->updateAndFetchParticipationCount($kuppiId, true);
    }

    public function removeParticipation($kuppiId, $userId) {

        if (!$this->isParticipating($kuppiId, $userId)) {
            return null;
        }
    
        // Delete set to false for permanent deletion
        $this->delete([
            ['user_id', '=', $userId],
            ['kuppi_id', '=', $kuppiId]
        ],
        false
    );

        return $this->updateAndFetchParticipationCount($kuppiId, false);
    }

    public function getParticipatorsForKuppi($kuppiId) {
        $participators = $this->where([['kuppi_id', '=', $kuppiId]]);
        return $participators ? $participators : [];
    }

    private function updateAndFetchParticipationCount($kuppiId, $increment = true) {
            require_once(__DIR__ . "/Kuppi.php");
            $kuppiModel = new KuppiModel();
            $kuppiModel->incrementParticipantCount($kuppiId, $increment);
            return $kuppiModel->getParticipantCount($kuppiId);
    }

}