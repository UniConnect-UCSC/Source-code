<?php

class StudyMaterialModel{
    
    use Model;
    protected $table = 'study_materials';

    public function getStudyMaterials($limit, $offset, $orderBy, $searchTerm = ''): array{

        $selected = ['m.id', 'm.title', 'm.description', ["t.name", "category"], 'm.type', 'm.view_count', 'm.created_at'];

        $conditions = [];

        if(!empty($searchTerm)){
            $conditions[] = ['m.title', 'ILIKE', '%' . $searchTerm . '%', 'OR'];
            $conditions[] = ['m.description', 'ILIKE', '%' . $searchTerm . '%', 'OR'];
            $conditions[] = ['t.name', 'ILIKE', '%' . $searchTerm . '%'];
        }

        $join = [
            ["study_categories", ["m.category_id = t.id"], "INNER", "t"],

        ];

        $orderByClause = ['m.created_at' => 'DESC'];

        switch($orderBy) {
            case 'recent':
                $orderByClause = ['m.created_at' => 'DESC'];
                break;
            case 'popular':
                $orderByClause = ['m.view_count' => 'DESC'];
                break;
            case 'title':
                $orderByClause = ['m.title' => 'ASC'];
                break;
        }

        error_log("Fetching study materials with orderBy: $orderBy, searchTerm: $searchTerm, limit: $limit, offset: $offset");
        $data = $this->where(
            conditions: $conditions,
            limit: $limit,
            offset: $offset,
            orderBy: $orderByClause,
            join: $join,
            selected: $selected
        );

        return $data ? $data : [];
    }

    public function createSM($data){
        return $this->insert(array_keys($data), array_values($data));
    }

    public function getLinkAndType($id){
        $result = $this->where(
            conditions: [['id', '=', $id]],
            selected: ['url', 'type']
        );

        if(!$result || empty($result[0])) {return null;}

         $data = [
            'url' => $result[0]->url ?? null,
            'type' => $result[0]->type ?? null
        ];

        return $data;
    }

    public function incrementViewCount($id){
        return $this->increment($id, 'view_count', 1, 'id') ? true : false; 
    }
}