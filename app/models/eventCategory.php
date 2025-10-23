<?php 
class EventCategoryModel{
    use Model;
    protected $table = 'event_categories';

    public function getAllCategories($limit, $offset){
        return $this->findAll(limit: $limit, offset: $offset);
    }
}