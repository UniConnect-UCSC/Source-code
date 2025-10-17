<?php

class KuppiCategoryModel {
    use Model;
    protected $table = 'kuppi_categories';

    public function getAllKuppiCategories() {
        return $this->findAll();
    }
}