<?php

class MarketplaceItem
{
    use Model;
    protected $table = 'marketplace_items';

    public function getItems()
    {
        return $this->findAll();
    }
    public function getMyitems($user_id)
    {
        $result = $this->where(conditions: [['student_id', '=', $user_id]], limit: 10);
        return $result;
    }
    public function getitemsById($id)
    {
        $result = $this->where(conditions: [['id', '=', $id]], limit: 1);
        return $result ? $result[0] : null;
    }
    public function getStatusOptions()
    {

        return ['Available', 'Sold', 'Reserved', 'Not Available'];
    }

    public function create($data)
    {
        $requiredFields = ['title', 'description', 'price', 'category_id', 'student_id', 'status'];
        return $this->insert($data);
    }

    public function updateItem($data)
    {
        $id_column = 'id';
        $id = $data[$id_column];
        unset($data[$id_column]); // Remove id from data to prevent updating it

        // Use the Model trait's update method
        return $this->update($id, $data, $id_column);
    }
    public function deleteItem($id)
    {
        return $this->delete($id);
    }
}