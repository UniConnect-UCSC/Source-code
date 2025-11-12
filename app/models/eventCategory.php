<?php 
class EventCategoryModel{
    use Model;
    protected $table = 'event_categories';

    public function getAllCategories($searchTerm, $excludeIds, $limit, $offset){

        $conditions = [
            ['name', 'ILIKE', "%$searchTerm%"]
        ];

        if(!empty($excludeIds)){
            $conditions[] = ['id', 'NOT IN', "(".implode(',', $excludeIds).")"];
        }

        return $this->where(
            conditions: $conditions,
            limit: $limit,
            offset: $offset
        );

    }
}