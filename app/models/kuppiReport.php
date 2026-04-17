<?php 

class KuppiReportModel {
    use Model;
    protected $table = "kuppi_reports";

    public function getMyReportedKuppiSessions ($userId ,$offset ,$limit) {
        $conditions = [
            ['k.host_id','=', $userId]
        ];

        $join = [
            ["kuppi", "m.kuppi_id = k.id", "INNER", "k"],
            ["users", "k.host_id = h.id", "INNER", "h"],
            ["users", "k.requester_id = r.id", "LEFT", "r"],
            ["kuppi_categories", "k.category_id = c.id", "INNER", "c"],
            ["universities", "h.university_id = n.id", "INNER" ,"n"],
            ["universities", "r.university_id = s.id", "LEFT" ,"s"]
            
        ];

        $orderBy = [
            "k.kuppi_date_time" => 'DESC'
        ];

        $selected = [
            "k.*",
            ["h.f_name" , "host_f_name"],
            ["r.f_name" , "requester_f_name"],
            ["h.l_name" , "host_l_name"],           
            ["r.l_name" , "requester_l_name"],
            ["c.category_name" , "category"],
            ["n.name" , "host_university"],
            ["s.name" , "requester_university"],
            ["count(m.id)", "report_count"]            
        ];

        $groupBy = [
        "k.id",
        "h.f_name",
        "r.f_name",
        "h.l_name",
        "r.l_name",
        "c.category_name",
        "n.name",
        "s.name"
    ];

        $data = $this->where(
            conditions: $conditions,
            join: $join,
            orderBy: $orderBy,
            offset: $offset,
            limit: $limit,
            selected: $selected,
            groupBy: $groupBy
        );

        if(!is_array($data)) {
            return [];
        }

        return $data;
    }
}