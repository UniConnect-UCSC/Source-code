<?php

final class Notification{
    public string $id = "";
    public string $type; // "system_error", "event_update" 
    public string $title;
    public string $message;
    public object|array $metadata; // Additional data related to the notification
    
    public function __construct(string $type, string $title, string $message, object|array $metadata) {
        $this->type = $type;
        $this->title = $title;
        $this->message = $message;
        $this->metadata = $metadata;
    }
}

interface NotificationChannelInterface {
    public function send(
        Notification $notification,
        RecipientInterface $recipient
    ): void;

    public function getChannelName(): string;
}


interface RecipientInterface {
    public function getId(): string;
}

interface RecipientProviderInterface {
    public function getRecipients(): iterable;     // return RecipientInterface[] or a Iterative object using Yield
    public function getConstructorInputs(): array;
}


class NotificationService {

    use Model;
    private string $table = "notification_jobs";
    private bool $instantNotificationsEnabled = false;

    public function notify(Notification $notification, RecipientProviderInterface $provider, array $channelNames): void {


        $jobData = [
            'notification_type' => $notification->type,
            'notification_title' => $notification->title,
            'notification_message' => $notification->message,
            'notification_metadata' => json_encode($notification->metadata),
            'recipient_provider_class' => get_class($provider),
            'recipient_provider_constructor_inputs' => json_encode($provider->getConstructorInputs()),
            'channel_names' => json_encode($channelNames)
        ]; 

        $this->insert(array_keys($jobData), array_values($jobData));
        error_log("Notification job queued: " . json_encode($jobData));

        if ($this->instantNotificationsEnabled) {
            $this->runNotificationJob();
        }

    }

    public function enableInstantNotifications(): void {
        $this->instantNotificationsEnabled = true;
    }

    public function runNotificationJob(): void {
        require_once(__DIR__ . '/../workers/notificationWorker.php');

        $channelNames = ["inAppNotification"]; 
        $worker = new NotificationWorker($channelNames);

        $worker->executeQueuedNotifications();
    }
}
