<?php

class Friendship
{
    use Model;
    protected $table = 'friendships';

    public function getFriends($user_id)
    {
        $result = $this->where(conditions: [
            ['user_id', '=', $user_id],
            ['status', '=', 'accepted']
        ]);
        return $result;
    }

    public function getFriendshipStatus($user_id, $friend_id)
    {
        $result = $this->where(conditions: [
            ['requester_id', '=', $user_id],
            ['addressee_id', '=', $friend_id]
        ], limit: 1);

        if (!$result) {
            return null;
        }

        return $result[0]->status ?? $result[0]['status'] ?? null;
    }

    public function isFriend($user_id, $friend_id)
    {
        $result = $this->where(conditions: [
            ['requester_id', '=', $user_id],
            ['addressee_id', '=', $friend_id],
            ['status', '=', 'accepted']
        ], limit: 1);

        return !empty($result);
    }

    public function getFriendRequests($user_id)
    {
        $result = $this->where(conditions: [
            ['friend_id', '=', $user_id],
            ['status', '=', 'pending']
        ]);
        return $result;
    }

    public function sendFriendRequest($user_id, $friend_id)
    {
        // Check if a friendship already exists
        $existing = $this->where(conditions: [
            ['requester_id', '=', $user_id],
            ['addressee_id', '=', $friend_id]
        ], limit: 1);

        if ($existing) {
            return false;
        }


        $result = $this->insertAndFetch([
            'requester_id' => $user_id,
            'addressee_id' => $friend_id,
            'status' => 'pending',
            'requested_at' => date('Y-m-d H:i:s')
        ]);

        return $result ? true : false;
    }

    public function removeFriendRequest($user_id, $friend_id)
    {
        $result = $this->where(conditions: [
            ['requester_id', '=', $user_id],
            ['addressee_id', '=', $friend_id]
        ], limit: 1);

        if (!$result) {
            return false;
        }

        return $this->delete($result[0]->id);
    }
}
