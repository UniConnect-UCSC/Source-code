<?php

require_once(__DIR__ . '/../recipients/userRecipient.php'); 

/**
 * A recipient provider that takes an array of user IDs and provides them as recipients.
 * This is useful for notifications that need to be sent to a specific set of users that the records are modified afterwards
 */
class deterministicMultiUserProvider implements RecipientProviderInterface {
    private array $arrayOfUserIds;
    private array $constructorInput;

    public function __construct(array $arrayOfUserIds) {
        $this->arrayOfUserIds = $arrayOfUserIds;
        $this->setConstructorInputs();
    }

    private function setConstructorInputs(): void {
        $this->constructorInput['arrayOfUserIds'] = $this->arrayOfUserIds;
    }

    public function getRecipients(): iterable{
        foreach ($this->arrayOfUserIds as $userId) {
            yield new userRecipient($userId);
        }
    }

    public function getConstructorInputs(): array {
        return $this->constructorInput;
    }
}

