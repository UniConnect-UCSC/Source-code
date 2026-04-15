<?php

class BoardingRoom
{
    use Model;
    protected $table = 'boarding_rooms';

    public function getRooms()
    {
        return $this->findAll();
    }




    public function getMyListings($user_id)
    {
        $result = $this->where(conditions: [['student_id', '=', $user_id]], limit: 10);
        return $result;
    }

    public function getRoomById($id)
    {
        $result = $this->where(conditions: [['id', '=', $id]], limit: 1);
        return $result ? $result[0] : null;
    }

    public function getStatusOptions()
    {
        return ['available', 'occupied', 'closed'];
    }


    public function getRoomsByLocation($location_id)
    {
        $result = $this->where(conditions: [['location_id', '=', $location_id]], limit: 20);
        return $result;
    }

    public function getAvailableRooms()
    {
        $result = $this->where(conditions: [['status', '=', 'available']], limit: 50);
        return $result;
    }

    public function getRoomsByRentRange($min_rent, $max_rent)
    {
        $result = $this->query(
            "SELECT * FROM {$this->table} WHERE rent >= :min_rent AND rent <= :max_rent AND status = 'available' ORDER BY rent ASC",
            ['min_rent' => $min_rent, 'max_rent' => $max_rent]
        );
        return $result;
    }

    public function create($data)
    {
        $requiredFields = ['student_id', 'rent', 'occupancy', 'status', 'location_id', 'description', 'contact_number'];
        return $this->insert($data);
    }

    public function updateRoom($data)
    {
        $id_column = 'id';
        $id = $data[$id_column];
        unset($data[$id_column]); // Remove id from data to prevent updating it

        // Add updated_at timestamp
        $data['updated_at'] = date('Y-m-d H:i:s');

        // Use the Model trait's update method
        return $this->update($id, $data, $id_column);
    }
}
