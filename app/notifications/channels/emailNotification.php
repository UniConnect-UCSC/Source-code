<?php

class EmailNotificationChannel implements NotificationChannelInterface {
    private string $channelName = 'email';

    public function send(Notification $notification, RecipientInterface $recipient): void {

    }

    public function getChannelName(): string {
        return $this->channelName;
    }
}