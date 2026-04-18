<?php

class PostComment
{
    use Model;
    protected $table = 'post_comments';

    public function getCommentsForPost($postId)
    {
        $sql = "
        SELECT
            pc.id,
            pc.post_id,
            pc.student_id,
            pc.comment_text,
            pc.commented_at,
            u.profile_picture,
            CONCAT(u.f_name, ' ', u.l_name) AS author
        FROM post_comments pc
        INNER JOIN users u ON u.id = pc.student_id
        WHERE pc.post_id = :post_id
        ORDER BY pc.commented_at ASC
    ";

        return $this->query($sql, ['post_id' => $postId]) ?: [];
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
