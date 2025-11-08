<?php 
class itemcategorymodel{
    use Model;
    protected $table = 'marketplace_categories';

    public function getAllCategories(){
        return $this->findAll();
    }
}