<?php

class PostComment
{
    use Model;
    protected $table = 'post_comments';

    public function getCommentsForPost($postId)
    {
        return $this->where([
            ['post_id', '=', $postId]
        ], null, null, ['commented_at' => 'ASC']);
    }

    public function addComment($userId, $postId, $commentText)
    {
        return $this->insertAndFetch([
            'student_id' => $userId,
            'post_id' => $postId,
            'comment_text' => $commentText,
            'commented_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function belongsToUser($commentId, $userId)
    {
        $comment = $this->first([
            ['id', '=', $commentId],
            ['student_id', '=', $userId]
        ]);

        return $comment ? true : false;
    }

    public function deleteComment($commentId, $userId)
    {
        // Only allow deletion if the comment belongs to the user
        return $this->delete([
            ['id', '=', $commentId],
            ['student_id', '=', $userId]
        ]);
    }
}
