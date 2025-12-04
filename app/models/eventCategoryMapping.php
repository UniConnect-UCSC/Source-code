<?php

class EventCategoryMappingModel{
    use Model;
    protected $table = 'event_category_mapping';

    function mapEventToCategory($eventId, $categories){

        $columns = ['event_id', 'category_id'];
        $data = [];
        foreach($categories as $categoryId){
            $data[] = [$eventId, $categoryId];
        }

        $this->insert($columns, $data);
    }


}