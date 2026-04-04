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

    function deleteMappingsForEvent($eventId){
        $this->delete([
            ['event_id', '=', $eventId]
        ]);
        return true;
    }

    function getCategoriesForEvent($eventId){
        $selected = ['c.id', 'c.name'];
        $join = [
            ["event_categories", "m.category_id = c.id", "INNER", "c"]
        ];
        $conditions = [
            ['m.event_id', '=', $eventId]
        ];

        return $this->where(
            conditions: $conditions,
            join: $join,
            selected: $selected
        );
    }
}