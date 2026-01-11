<?php

require_once(__DIR__ . '/../recipients/userRecipient.php'); 

class FavEventRecipientProvider implements RecipientProviderInterface {
    private string $eventId;
    private $constructorInput;
    
    public function __construct(string $eventId){
        $this->eventId = $eventId;
        $this->setConstructorInputs();
    }

    private function setConstructorInputs(): void {
        $this->constructorInput['eventId'] = $this->eventId;
    }

    public function getRecipients(): iterable {
        // Grab from DB
        $userIds = ["UUID-01", "UUID-02", "UUID-03", "UUID-04"];

        foreach ($userIds as $userId) {
            yield new userRecipient($userId);
        }
    }
    
    public function getConstructorInputs(): array {
        return $this->constructorInput;
    }
}

