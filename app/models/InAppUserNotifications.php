<?php

class NotificationModel {
    use Model;
    protected $table = 'in_app_user_notifications';

    private function getNextNotifications($limit, $offset, $unreadOnly = false) {
        $conditions = [
            ['m.user_id', '=', $_SESSION['user_id']]
        ];
        if ($unreadOnly) {
            $conditions[] = ['m.is_read', '=', 0];
        }

        $selected = [
            "m.*",
            "n.type",
            "n.title",
            "n.message",
            "n.metadata",
        ];

        $join = [
            [
                "in_app_notifications",
                "m.notification_id = n.id",
                "INNER",
                "n"
            ]
        ];

        error_log("Fetching notifications for user_id: " . $_SESSION['user_id'] . " with limit: $limit and offset: $offset");
        return $this->where(
            conditions: $conditions,
            limit: $limit,
            offset: $offset,
            orderBy: ['created_at' => 'DESC'],
            join: $join,
            selected: $selected
        );
    }

    public function getNextAllNotifications($limit, $offset) {
        return $this->getNextNotifications($limit, $offset, false);
    }
    
    public function getNextUnreadNotifications($limit, $offset) {
        return $this->getNextNotifications($limit, $offset, true);
    }

    public function markAsRead($notificationId) {
        $this->update(
            id: $notificationId,
            data: [
                'is_read' => 1,
                'read_at' => date('Y-m-d H:i:s', time())
            ],
            id_column: 'id'
        );

        return true;
    }

    // ISSUE: Time zone issue can really affect the read_at timestamp if user is in different timezone
    // Migate to NOW() in SQL

    public function markAllAsRead($userId) {
        $this->update(
            id: $userId,
            data: [
                'is_read' => 1,
                'read_at' => date('Y-m-d H:i:s', time())
            ],
            id_column: 'user_id'
        );

        // update successful-ness isn't being passed by the database/query method
        return true;
    }

    public function hasNewNotifications($userId, $lastCheckTimestamp) {
            $conditions = [
                ['user_id', '=', $userId],
                ['created_at', '>', $lastCheckTimestamp],
            ];
            
            $selected = [
                ["COUNT(*)", "new_notification_count"]
            ];

            $result = $this->where(
                conditions: $conditions,
                selected: $selected
            );

            return $result[0]->new_notification_count > 0;
    }

    public function getUnreadCount($userId) {
        $conditions = [
            ['user_id', '=', $userId],
            ['is_read', '=', 0],
        ];
        
        $selected = [
            ["COUNT(*)", "unread_notification_count"]
        ];

        $result = $this->where(
            conditions: $conditions,
            selected: $selected
        );

        return (int)$result[0]->unread_notification_count;
    }
}