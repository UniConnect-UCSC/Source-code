<?php 
class KuppiModel {
    use Model;
    protected $table = 'kuppi';

    public function getKuppi($offset, $limit){
       $conditions = [
        ['m.status','=','In Progress']
        ];
    
        $join = [
            ["universities", "m.university_id = u.id", "INNER", "u"],
            ["users", "m.host_id = h.id", "INNER", "h"],
            ["users", "m.requester_id = r.id", "LEFT", "r"],
            ["kuppi_categories","m.category_id = c.id", "INNER" ,"c"],
            ["kuppi_participants",["m.id = p.kuppi_id",["p.user_id", "=", $_SESSION["user_id"]]],"LEFT","p"]
        ];
        $orderBy = [
            "m.kuppi_date_time" =>'DESC'
        ];

        $selected = [
            "m.*",
            ["u.name", "university"],
            ["h.f_name", "host_f_name"],
            ["h.l_name", "host_l_name"],
            ["r.f_name", "requester_f_name"],
            ["r.l_name", "requester_l_name"],
            ["CASE WHEN p.kuppi_id IS NULL THEN 0 ELSE 1 END", "is_participating"]
        ];


        $data = $this->where(
            conditions: $conditions,
            orderBy: $orderBy,
            limit: $limit,
            offset: $offset,
            join: $join,
            selected: $selected,
        );

        if(!is_array($data)) {
            return [];
        }
        return $data;
       
    }
    
    public function getMyHosts($offset ,$limit ,$status = null){
        $conditions = [
            ['m.host_id','=',$_SESSION['user_id']],       
        ];
        
        if ($status) {
            $conditions[] = ['m.status', '=', $status];
        }

        $join = [
            ["users","m.host_id = h.id", "INNER","h"],
            ["users","m.requester_id = r.id", "LEFT" ,"r"],
            ["kuppi_categories","m.category_id = c.id", "INNER" ,"c"],
            ["universities", "h.university_id = n.id", "INNER" ,"n"],
            ["universities", "r.university_id = s.id", "LEFT" ,"s"]
        ];
        $orderBy = [
            "m.kuppi_date_time" =>'DESC'
        ];

        $selected = [
            "m.*",
            ["h.f_name" , "host_f_name"],
            ["r.f_name" , "requester_f_name"],
            ["h.l_name" , "host_l_name"],           
            ["r.l_name" , "requester_l_name"],
            ["c.category_name" , "category"],
            ["n.name" , "host_university"],
            ["s.name" , "requester_university"]
        ];
        $data = $this->where(
            conditions: $conditions,
            join: $join,
            orderBy: $orderBy,
            offset: $offset,
            limit: $limit,
            selected: $selected
        );

        if(!is_array($data)) {
            return [];
        }

        return $data;
    }

    public function getParticipantCount($kuppiId) {

        $kuppi = $this->getKuppiById($kuppiId);
        return $kuppi ? $kuppi->participants : null;
    }

    public function incrementParticipantCount($kuppiId, $incrementType = true) {

        $amount = $incrementType ? 1 : -1;
        return $this->increment(id: $kuppiId, column: 'participants', amount: $amount);

    }

    public function getMyParticipations($offset, $limit, $status = null) {
        $conditions = [
            ['p.user_id', '=', $_SESSION['user_id']]
        ];

        if ($status) {
            $conditions[] = ['m.status', '=', $status];
        }

        $join = [
            ["kuppi_participants", "m.id = p.kuppi_id", "INNER", "p"],
            ["universities", "m.university_id = u.id", "INNER", "u"],
            ["users", "m.host_id = h.id", "INNER", "h"],
            ["users", "m.requester_id = r.id", "LEFT", "r"],
            ["kuppi_categories", "m.category_id = c.id", "INNER", "c"]
        ];

        $orderBy = [
            "m.kuppi_date_time" => 'DESC'
        ];

        $selected = [
            "m.*",
            ["u.name", "university"],
            ["h.f_name", "host_f_name"],
            ["h.l_name", "host_l_name"],
            ["r.f_name", "requester_f_name"],
            ["r.l_name", "requester_l_name"],
            ["c.category_name", "category"]
        ];

        $data = $this->where(
            conditions: $conditions,
            join: $join,
            orderBy: $orderBy,
            offset: $offset,
            limit: $limit,
            selected: $selected
        );

        if (!is_array($data)) {
            return [];
        }

        return $data;
    }

    public function getMyRequests($offset, $limit ,$status = null){
        $conditions = [
            ['m.requester_id','=',$_SESSION['user_id']],  
        ];
        if ($status) {
            $conditions[] = ['m.status', '=', $status];
        }

        $join = [
            ["users","m.requester_id = r.id", "INNER" ,"r"],
            ["users","m.host_id = h.id", "LEFT","h"],
            ["kuppi_categories","m.category_id = c.id", "INNER" ,"c"],
            ["universities", "h.university_id = n.id", "LEFT" ,"n"],
            ["universities", "r.university_id = s.id", "INNER" ,"s"]
        ];

        $orderBy = [
            "m.kuppi_date_time" =>'DESC'
        ];
        $selected = [
            "m.*",
            ["h.f_name" , "host_f_name"],
            ["r.f_name" , "requester_f_name"],
            ["h.l_name" , "host_l_name"],           
            ["r.l_name" , "requester_l_name"],
            ["c.category_name" , "category"],
            ["n.name" , "host_university"],
            ["s.name" , "requester_university"]
        ];
        $data = $this->where(
            conditions: $conditions,
            join: $join,
            offset: $offset,
            orderBy: $orderBy,
            limit: $limit,
            selected: $selected
        );

        if(!is_array($data)) {
            return [];
        }

        return $data;

    }

    public function getKuppiById($id){
        $result = $this->where(conditions: [['id','=',$id]], limit: 1);
        return $result ? $result[0] : null;
    }

    public function updateKuppi($data)
    {
        $requiredFields = ['updated_at'];

        // Validate required fields
        if (!$this->validate($data, $requiredFields)) {
            return false;
        }

        $id_column = 'id';
        $id = $data[$id_column];
        unset($data[$id_column]); // Remove id from data to prevent updating it

        // Use the Model trait's update method
        return $this->update($id, $data, $id_column);
    }

    public function getKuppiRequests($offset, $limit){
        $conditions = [
            ['m.status', '=', 'Requested']
        ];

        $join = [
            ['users', 'm.requester_id = r.id', 'INNER', 'r'],
            ['universities', 'r.university_id = n.id', 'INNER', 'n'],
            ['kuppi_categories', 'm.category_id = c.id', 'INNER', 'c'],
        ];

        $selected = [
            'm.*',
            ['r.f_name', 'requester_f_name'],
            ['r.l_name', 'requester_l_name'],
            ['n.name', 'requester_university'],
            ['c.category_name', 'category'],
        ];

        $data = $this->where(
            conditions: $conditions,
            offset: $offset,
            limit: $limit,
            join: $join,
            selected: $selected
        );

        if (!is_array($data)) {
            return [];
        }
        return $data;
    }
}
