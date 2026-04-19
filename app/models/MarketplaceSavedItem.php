<?php

class MarketplaceSavedItem
{
    use Model;
    protected $table = 'marketplace_saved_items';

    public function existsForUser(string $studentId, string $itemId)
    {
        return $this->first([
            'student_id' => $studentId,
            'marketplace_item_id' => $itemId,
        ]);
    }

    public function addForUser(string $studentId, string $itemId)
    {
        $existing = $this->existsForUser($studentId, $itemId);
        if ($existing) return $existing;

        $sql = "INSERT INTO {$this->table} (marketplace_item_id, student_id, created_at)
                VALUES (:item_id, :student_id, NOW())
                RETURNING *";

        return $this->get_row($sql, [
            'item_id' => $itemId,
            'student_id' => $studentId,
        ]);
    }

    public function removeForUser(string $studentId, string $itemId)
    {
        $sql = "DELETE FROM {$this->table}
                WHERE student_id = :student_id AND marketplace_item_id = :item_id
                RETURNING *";

        return $this->get_row($sql, [
            'student_id' => $studentId,
            'item_id' => $itemId,
        ]);
    }

    public function listDetailedForUser(string $studentId, int $limit = 50)
    {
        $limit = max(1, min(200, (int)$limit));

        $sql = "
            SELECT
                s.marketplace_item_id AS id,
                mi.title,
                mi.price,
                mi.status,
                mi.category_id,
                s.created_at AS saved_at,
                (
                    SELECT image_url
                    FROM marketplace_item_images
                    WHERE marketplace_item_id = mi.id
                    ORDER BY id DESC
                    LIMIT 1
                ) AS image_url
            FROM {$this->table} s
            JOIN marketplace_items mi ON mi.id = s.marketplace_item_id
            WHERE s.student_id = :student_id
            ORDER BY s.created_at DESC
            LIMIT {$limit}
        ";

        return $this->query($sql, ['student_id' => $studentId]) ?: [];
    }
}