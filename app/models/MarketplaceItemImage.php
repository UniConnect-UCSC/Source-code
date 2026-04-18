<?php

class MarketplaceItemImage
{
    use Model;
    protected $table = 'marketplace_item_images';

    public function getByItemId($itemId)
    {
        return $this->where([['marketplace_item_id', '=', $itemId]], null, null, ['id' => 'ASC']) ?: [];
    }

    public function deleteByIdForItem($imageId, $itemId)
    {
        $sql = "DELETE FROM {$this->table}
                WHERE id = :image_id AND marketplace_item_id = :item_id
                RETURNING id";

        return $this->get_row($sql, [
            'image_id' => $imageId,
            'item_id' => $itemId,
        ]);
    }
}