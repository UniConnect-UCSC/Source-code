<?php

class userRecipient implements RecipientInterface {
    private string $userId;

    public function __construct(string $userId) {
        $this->userId = $userId;
    }

    public function getId(): string {
        return $this->userId;
    }
}