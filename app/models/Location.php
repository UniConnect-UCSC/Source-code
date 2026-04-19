<?php 
class Location{
    use Model;
    protected $table = 'locations';

    public function getAllLocations(){
        return $this->findAll();
    }

    public function getLocationsByDistrict($district){
        return $this->where([['district', '=', $district]]);
    }

    public function getDistinctDistricts(){
        $query = "SELECT DISTINCT district FROM {$this->table} ORDER BY district ASC";
        return $this->query($query);
    }

    public function getDistinctCities(){
        $query = "SELECT DISTINCT city FROM {$this->table} ORDER BY city ASC";
        return $this->query($query);
    }

    public function findOrCreateLocation($city, $district){
        // Try to find existing location
        $existing = $this->first([
            ['city', '=', $city],
            ['district', '=', $district]
        ]);

        if ($existing) {
            return $existing;
        }

        // Create new location if not found
        $data = [
            'city' => $city,
            'district' => $district
        ];
        
        return $this->insertAndFetch($data);
    }
}