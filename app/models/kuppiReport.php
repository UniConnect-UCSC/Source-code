<?php

class KuppiReportModel
{
    use Model;
    protected $table = "kuppi_reports";

    public function getAllKuppiReports($offset = 0, $limit = null, $status = null, $search = '')
    {
        $conditions = [
            ['m.kuppi_id', 'IS', 'NOT NULL']
        ];

        if (!empty($status)) {
            $conditions[] = ['m.report_status', '=', $status];
        }

        if ($search !== '') {
            $conditions[] = ['k.topic', 'ILIKE', '%' . $search . '%', 'OR'];
            $conditions[] = ['k.status', 'ILIKE', '%' . $search . '%', 'OR'];
            $conditions[] = ['h.f_name', 'ILIKE', '%' . $search . '%', 'OR'];
            $conditions[] = ['h.l_name', 'ILIKE', '%' . $search . '%', 'OR'];
            $conditions[] = ['c.category_name', 'ILIKE', '%' . $search . '%', 'OR'];
            $conditions[] = ['n.name', 'ILIKE', '%' . $search . '%', 'OR'];
            $conditions[] = ['CAST(k.id AS TEXT)', 'ILIKE', '%' . $search . '%'];
        }

        $join = [
            ["kuppi", "m.kuppi_id = k.id", "INNER", "k"],
            ["users", "k.host_id = h.id", "INNER", "h"],
            ["users", "k.requester_id = r.id", "LEFT", "r"],
            ["kuppi_categories", "k.category_id = c.id", "INNER", "c"],
            ["universities", "h.university_id = n.id", "INNER", "n"],
            ["universities", "r.university_id = s.id", "LEFT", "s"]
        ];

        $selected = [
            "m.report_status",
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
        "m.report_status",
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
            selected: $selected,
            groupBy: $groupBy,
            orderBy: $orderBy,
            offset: $offset,
            limit: $limit
        );

        return is_array($data) ? $data : [];
    }

    public function hasPendingReports($kuppiId)
    {
        $sql = "SELECT COUNT(*) AS count
                FROM kuppi_reports
                WHERE kuppi_id = :kuppi_id
                  AND report_status = 'Pending'";

        $row = $this->get_row($sql, ['kuppi_id' => $kuppiId]);
        return $row && (int)$row->count > 0;
    }

    public function resolveReportsForKuppi($kuppiId, $decision)
    {
        $sql = "UPDATE kuppi_reports
                SET report_status = 'Resolved',
                    decision = :decision
                WHERE kuppi_id = :kuppi_id
                  AND report_status = 'Pending'
                RETURNING id";

        $rows = $this->query($sql, [
            'decision' => $decision,
            'kuppi_id' => $kuppiId
        ]);

        return is_array($rows) && count($rows) > 0;
    }
}
