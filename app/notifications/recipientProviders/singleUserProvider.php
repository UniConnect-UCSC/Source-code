<?php

require_once(__DIR__ . '/../recipients/userRecipient.php'); 

class singleUserProvider implements RecipientProviderInterface {
    private string $userId;
    private array $constructorInput;

    public function __construct(string $userId) {
        $this->userId = $userId;
        $this->setConstructorInputs();
    }

    private function setConstructorInputs(): void {
        $this->constructorInput['userId'] = $this->userId;
    }

    public function getRecipients(): array{
        return [ new userRecipient($this->userId) ];
    }

    public function getConstructorInputs(): array {
        return $this->constructorInput;
    }
}

