<?php

class Reaction
{
    use Model;
    protected $table = 'reactions';

    public function countReactions($postId)
    {
        return $this->count([
            ['post_id', '=', $postId]
        ]);
    }

    public function getReaction($userId, $postId)
    {
        return $this->first([
            'student_id' => $userId,
            'post_id' => $postId
        ]);
    }

    public function removeReaction($userId, $postId)
    {
        return $this->delete([
            ['student_id', '=', $userId],
            ['post_id', '=', $postId]
        ]);
    }

    public function addReaction($userId, $postId, $postType)
    {
        return $this->insertAndFetch([
            'student_id' => $userId,
            'post_id' => $postId,
            'post_type' => $postType,
            'reacted_at' => date('Y-m-d H:i:s')
        ]);
    }
}
