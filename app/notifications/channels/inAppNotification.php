<?php

class inAppNotificationChannel implements NotificationChannelInterface {
    use Model;
    private string $channelName = 'in_app';
    protected string $table = "in_app_user_notifications";

    public function getChannelName(): string {
        return $this->channelName;
    }

    public function send(Notification $notification, RecipientInterface $recipient): void {
        
        // Save into notification table
        if($notification->id === null) {
            // Insert into notification table
            $inAppNotificationModel = new inAppNotificationModel();
            $notification->id = $inAppNotificationModel->addNewNotification($notification);
        }

        $this->sendUserNotification($notification->id, $recipient->getId()); 
        error_log("In-App Notification sent to User ID: " . $recipient->getId() . " for Notification ID: " . $notification->id);
    }

    private function sendUserNotification(string $notificationId, string $userId){
        $data = [
            'notification_id' => $notificationId,
            'user_id' => $userId,
        ];

        $this->insert(array_keys($data), array_values($data)); 
    }

}

class inAppNotificationModel {
    use Model;
    protected string $table = "in_app_notifications";

    public function addNewNotification(Notification $notification): string {
        
        $data = [
            'type' => $notification->type,
            'title' => $notification->title,
            'message' => $notification->message,
        ];

        if(!empty($notification->metadata)){
            $data['metadata'] = json_encode($notification->metadata);
        }

        $query = $this->insertAndFetch($data);
        return $query->id;
    }

}