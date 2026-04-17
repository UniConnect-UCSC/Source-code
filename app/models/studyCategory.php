<?php

class StudyCategoryModel {
    use Model;
    protected $table = 'study_categories';

    public function getAllCategories($searchTerm, $limit, $offset) {
        $conditions = [
            ['name', 'ILIKE', "%$searchTerm%"]
        ];

        return $this->where(
            conditions: $conditions,
            limit: $limit,
            offset: $offset
        );
    }
}