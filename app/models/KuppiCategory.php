<?php

class KuppiCategoryModel {
    use Model;
    protected $table = 'kuppi_categories';

    public function getAllKuppiCategories() {
        return $this->findAll();
    }
    public function getKuppiCategoryById($category_id) {
        $result = $this->where(conditions: [['id', '=', $category_id]], limit: 1);
        return $result ? $result[0] : null;
    }

    public function getKuppiCategories($offset ,$limit) {
        return $this->where(
            conditions: [['id', 'IS', 'NOT NULL']],
            limit: $limit,
            offset: $offset,
        );
    }
}