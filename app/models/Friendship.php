<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/University.php';


class Friendship
{
    use Model;
    protected $table = 'friendships';

    public function getFriends($user_id)
    {
        $asRequester = $this->where(conditions: [
            ['requester_id', '=', $user_id],
            ['status', '=', 'accepted']
        ]) ?: [];

        $asAddressee = $this->where(conditions: [
            ['addressee_id', '=', $user_id],
            ['status', '=', 'accepted']
        ]) ?: [];

        // Enrich with user details
        $userModel = new User();
        $universityModel = new University();

        foreach ($asRequester as &$friendship) {
            $friend = $userModel->first(['id' => $friendship->addressee_id]);
            if ($friend) {
                $friendship->friend_details = [
                    'id' => $friend->id,
                    'profile_picture' => $friend->profile_picture ?? '',
                    'firstname' => $friend->f_name ?? '',
                    'lastname' => $friend->l_name ?? '',
                    'university' => $universityModel->first(['id' => $friend->university_id])->name ?? 'University not set'
                ];
            }
        }

        foreach ($asAddressee as &$friendship) {
            $friend = $userModel->first(['id' => $friendship->requester_id]);
            if ($friend) {
                $friendship->friend_details = [
                    'id' => $friend->id,
                    'profile_picture' => $friend->profile_picture ?? '',
                    'firstname' => $friend->f_name ?? '',
                    'lastname' => $friend->l_name ?? '',
                    'university' => $universityModel->first(['id' => $friend->university_id])->name ?? 'University not set'
                ];
            }
        }

        return array_merge($asRequester, $asAddressee);
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
        $requests = $this->where(conditions: [
            ['addressee_id', '=', $user_id],
            ['status', '=', 'pending']
        ]) ?: [];

        if (empty($requests)) {
            return [];
        }

        $userModel = new User();
        $universityModel = new University();
        $enriched = [];

        foreach ($requests as $request) {
            $sender = $userModel->first(['id' => $request->requester_id]);
            if (!$sender) {
                continue;
            }

            $enriched[] = [
                'id' => $sender->id,
                'profile_picture' => $sender->profile_picture ?? '',
                'firstname' => $sender->f_name ?? '',
                'lastname' => $sender->l_name ?? '',
                'university' => $universityModel->first(['id' => $sender->university_id])->name ?? 'University not set'
            ];
        }

        return $enriched;
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

    public function acceptFriendRequest($user_id, $friend_id)
    {
        $sql = "UPDATE {$this->table} 
            SET status = 'accepted', responded_at = :responded_at 
            WHERE requester_id = :requester_id 
              AND addressee_id = :addressee_id 
              AND status = 'pending'";

        $data = [
            'responded_at' => date('Y-m-d H:i:s'),
            'requester_id' => $friend_id,
            'addressee_id' => $user_id
        ];

        $updated = $this->query($sql, $data);

        return (bool) $updated;
    }

    public function removeFriendRequest($user_id, $friend_id)
    {
        $deletedForward = $this->delete([
            ['requester_id', '=', $user_id],
            ['addressee_id', '=', $friend_id],
            ['status', '=', 'pending']
        ]);

        $deletedReverse = $this->delete([
            ['requester_id', '=', $friend_id],
            ['addressee_id', '=', $user_id],
            ['status', '=', 'pending']
        ]);

        return (bool) ($deletedForward || $deletedReverse);
    }

    public function removeFriendship($user_id, $friend_id)
    {
        $deletedForward = $this->delete([
            ['requester_id', '=', $user_id],
            ['addressee_id', '=', $friend_id],
            ['status', '=', 'accepted']
        ]);

        $deletedReverse = $this->delete([
            ['requester_id', '=', $friend_id],
            ['addressee_id', '=', $user_id],
            ['status', '=', 'accepted']
        ]);

        return (bool) ($deletedForward || $deletedReverse);
    }
}
