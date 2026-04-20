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
            ["count(m.kuppi_id)", "report_count"]            
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
   /*
    * report_status ENUMS : Pending , Resolved
    * decision ENUMS : "Kuppi Approved" , "Kuppi Rejected"
    * if decision made by admin : report_status -> Resolved , Decision -> Kuppi Rejected or Kuppi Approved
    * if kuppi rejected should change the kuppi Status to Rejected
    *
   */
    public function getAllKuppiReports ($offset = 0, $limit = null) {
    $conditions = [
        ['m.kuppi_id', 'IS', 'NOT NULL']
    ];

    $join = [
        ["kuppi", "m.kuppi_id = k.id", "INNER", "k"],
        ["users", "k.host_id = h.id", "INNER", "h"],
        ["users", "k.requester_id = r.id", "LEFT", "r"],
        ["kuppi_categories", "k.category_id = c.id", "INNER", "c"],
        ["universities", "h.university_id = n.id", "INNER", "n"],
        ["universities", "r.university_id = s.id", "LEFT", "s"]
    ];

    $selected = [
        "k.*",
        ["h.f_name", "host_f_name"],
        ["h.l_name", "host_l_name"],
        ["r.f_name", "requester_f_name"],
        ["r.l_name", "requester_l_name"],
        ["c.category_name", "category"],
        ["n.name", "host_university"],
        ["s.name", "requester_university"],
        ["COUNT(DISTINCT m.reporter_id)", "report_count"],
        ["MAX(m.created_at)", "last_reported_at"],
        ["MIN(m.created_at)", "first_reported_at"]
    ];

    $groupBy = [
        "k.id",
        "h.f_name",
        "h.l_name",
        "r.f_name",
        "r.l_name",
        "c.category_name",
        "n.name",
        "s.name"
    ];

    $orderBy = [
        "MAX(m.created_at)" => "DESC"
    ];

    $data = $this->where(
        conditions: $conditions,
        join: $join,
        selected: $selected,
        groupBy: $groupBy,
        orderBy: $orderBy,
        offset: $offset,
        limit: $limit
    );

    return is_array($data) ? $data : [];
    }
}