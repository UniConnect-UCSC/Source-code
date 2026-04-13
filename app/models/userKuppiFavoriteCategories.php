<?php 

class UserKuppiFavoriteCategoriesModel {
    use Model;

    protected $table = 'user_kuppi_favorite_categories';

    public function getAllKuppiFavoriteCategories () {
        return $this->findAll();
    }
    public function isAlreadyMapped($userId , $categoryId) {
        $result = $this->first(['user_id' => $userId, 'category_id' => $categoryId]);
        return $result ? true : false;
    }
    
    public function createUserFavoriteCategory($userId ,$categoryId) {
        return $this->insert(['user_id', 'category_id'], [$userId, $categoryId]);
    }

    public function removeUserFavoriteCategory($userId, $categoryId) {
        return $this->delete([
            ['user_id', '=', $userId],
            ['category_id', '=', $categoryId]
        ]);
    }

    public function getUserFavoriteCategories($offset, $limit) {
    $conditions = [
        ['m.user_id', '=',$_SESSION['user_id']]
    ];

    $join = [
        ["kuppi_categories", "m.category_id = c.id", "INNER", "c"]
    ];

    $data = $this->where(
        conditions: $conditions,
        join: $join,
        offset: $offset,
        limit: $limit
    );

    if(!is_array($data)) {
        return [];
    }

    return $data;
    }

    
}