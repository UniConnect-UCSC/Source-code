<?php

require_once(__DIR__ . "/../notifications/recipientProviders/singleUserProvider.php");
require_once(__DIR__ . "/../notifications/recipientProviders/multiUserProvider.php");

class KuppiNotificationHandler {
    private object $kuppi;

    public function __construct($kuppi) {
        if (is_array($kuppi)) {
            $kuppi = (object)$kuppi;
        }

        if (!is_object($kuppi) || empty($kuppi->id)) {
            throw new InvalidArgumentException('Invalid kuppi data');
        }

        $this->kuppi = $kuppi;
    }

    private function queueNotification(
        Notification $notification,
        RecipientProviderInterface $provider
    ): void {
        global $notificationService;

        if (!isset($notificationService)) {
            throw new RuntimeException('Notification service not available');
        }

        $notificationService->notify($notification, $provider, ['in_app']);
    }

    public function notifyRequester($type ,$title ,$message ,$metadata = []) {
        if (empty($this->kuppi->requester_id)) {
            return ['success' => true, 'message' => 'No requester to notify'];
        }

        $defaultMetadata = [
            'url' => '/kuppi',
            'kuppi_id' => (string)$this->kuppi->id,
        ];

        $notification = new Notification(
            type: $type ,
            title: $title ,
            message: $message ,
            metadata: array_merge($defaultMetadata, $metadata)
        );

        $provider = new singleUserProvider((string)$this->kuppi->requester_id);
        $this->queueNotification($notification, $provider);

        return ['success' => true, 'message' => 'Requester notification queued'];
    }

    public function notifyHost($type ,$title ,$message ,$metadata ) {

        $defaultMetadata = [
            'url' => '/kuppi',
            'kuppi_id' => (string)$this->kuppi->id,
        ];

        $notification = new Notification(
            type: $type ,
            title: $title ,
            message: $message ,
            metadata: array_merge($defaultMetadata, $metadata)
        );

        $provider = new singleUserProvider((string)$this->kuppi->host_id);
        $this->queueNotification($notification, $provider);

        return ['success' => true, 'message' => 'Host notification queued'];
    }

    public function notifyParticipants($type ,$title ,$message ,$metadata = []) {
        $defaultMetadata = [
            'url' => '/kuppi',
            'kuppi_id' => (string)$this->kuppi->id,
        ];

        $notification = new Notification(
            type: $type ,
            title: $title ,
            message: $message ,
            metadata: array_merge($defaultMetadata, $metadata)
        );

        $provider = new multiUserProvider(
            idColumnName: 'user_id',
            tableName: 'kuppi_participants',
            whereConstructorInputs: [
                'conditions' => [['kuppi_id', '=', (string)$this->kuppi->id]],
            ]
        );

        $this->queueNotification($notification, $provider);

        return ['success' => true, 'message' => 'Participant notifications queued'];
    }

    public function notifyFavorites($type ,$title ,$message ,$metadata = []){
        $defaultMetadata = [
            'url' => '/kuppi',
            'kuppi_id' => (string)$this->kuppi->id,
        ];

        $notification = new Notification(
            type: $type ,
            title: $title ,
            message: $message ,
            metadata: array_merge($defaultMetadata, $metadata)
        );

        $provider = new multiUserProvider(
            idColumnName: 'user_id',
            tableName: 'kuppi_favorites',
            whereConstructorInputs: [
                'conditions' => [['kuppi_id', '=', (string)$this->kuppi->id]],
            ]
        );

        $this->queueNotification($notification, $provider);

        return ['success' => true, 'message' => 'Favorite notifications queued'];
    }

}
