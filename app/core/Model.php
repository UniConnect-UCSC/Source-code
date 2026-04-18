<?php

trait Model
{
    use Database;

    public $errors = [];

    private $limit = 10;
    private $offset = 0;
    private $defaultIdColumn = 'id';
    private $softDeleteColumn = 'deleted_at';

    public function __construct()
    {
        $this->connect();
    }

    public function findAll($limit = null, $offset = null)
    {
        $limit = $limit ? $limit : $this->limit;
        $offset = $offset ? $offset : $this->offset;
        $sql = "SELECT * from $this->table limit $limit offset $offset";
        return $this->query($sql);
    }

    /**
     * @param array $conditions Array of conditions in format [field ,operator, value, next_logical_operator(AND/OR, default AND)]] 
     * next_logical_operator. For example: [ ["field1", "=", "value1", "OR"], ["field2", ">", "value2"] ] translates to "WHERE field1 = value1 OR field2 > value2"
     * WARNING!! Do not use next_logical_operator for the last condition!
     * 
     * When passing array values for IN/NOT IN operators, use: normal array for value
     * @param int $limit [DEFAULT: queries EVERYTHING]
     * @param int $offset [DEFAULT: 0]
     * @param array $orderBy Array of fields to order by with direction [field => 'ASC|DESC']
    * @param array|string $groupBy Array or single field (alias.column) to group by
     * @param array $join Array of join definitions in format [[ <join_table_name>, <join_condition>, <JOIN_TYPE>, <alias> ] , [] ,]
     * @param array $selected Array of field and aliases(not required)  [[<Column_name>, <Alias>], [], ..] OR ["column_","",...]
     * @param bool $showDeleted If true, includes soft-deleted records; if false, excludes them
     * DEFAULT join_condition is main_table.id = join_table.id
     * DEFAULT JOIN_TYPE is INNER
     * DEFAULT alias is none
     * Note(JOIN-Condition): can be either a string(Ex: "u.event_id = j.id") or an array of conditions in format ["u.event_id = j.id", [field ,operator, value]] (useful for parameterized join conditions)
     * 
     * Note(CONDITIONS): In this method only the condition inputs are prepared for execution
     * Note(JOIN): make sure to add the aliases if join is used. the main table is aliased as 'm'
     * NOTE(JOIN): if the joining tables have the same column name the result will have the last one overwriting previous ones
     */
    public function where($conditions, $limit = null, $offset = null, $orderBy = [], $join = [], $selected = [], $showDeleted = true, $groupBy = null)
    {
        try {
            $operators = ['=', '!=', '<', '>', '<=', '>=', 'LIKE', 'ILIKE', 'NOT IN', 'IN', 'IS'];
            $allowedIsValues = ['NULL', 'NOT NULL', 'TRUE', 'FALSE'];
            $joinTypes = ['INNER', 'LEFT', 'RIGHT', 'FULL'];
            $mainTableAlias = 'm';
            $joined = false;
            $softDeleteColumn = $this->softDeleteColumn;

            $data = [];

            $sql = "SELECT "; 

            //Handle selections
            if(!empty($selected)){
                foreach($selected as $column){
                    
                    if(is_array($column)){

                        $sql .= "$column[0] AS $column[1], ";
                    }else{

                        $sql .=  "$column, ";
                    }
                }
                $sql = substr($sql, 0, -2);
                $sql .= " ";

            }else{
                $sql .= "* ";
            }

            $sql .= "FROM {$this->table} AS {$mainTableAlias} ";

            // Handle JOINs
            if (!empty($join)) {
                $joined = true;
            }

            foreach($join as $joinCount => $joinItem){
                
                $joinType = in_array($joinItem[2], $joinTypes) ? $joinItem[2] : 'INNER';
                $alias = (isset($joinItem[3]) && !empty($joinItem[3])) ? " AS {$joinItem[3]} " : "";
                $joinCondition = (isset($joinItem[1]) && !empty($joinItem[1])) ? $joinItem[1] : "{$this->table}.id = {$joinItem[0]}.id";

                $sql .= "{$joinType} JOIN {$joinItem[0]}{$alias} ON ";

                // ["m.university_id", "=", "u.id"]
                if(is_array($joinCondition)){
                    foreach($joinCondition as $joinCondCount => $cond){
                        
                        if(is_array($cond)){
                            $key = "join_{$joinCount}_cond_{$joinCondCount}";
                            $data[$key] = $cond[2];
                            $sql .= "{$cond[0]} {$cond[1]} :{$key} AND ";
                        }else{
                            $sql .= "{$cond} AND ";
                        }

                    }
                    $sql = substr($sql, 0, -4);
                }else{
                    $sql .= "{$joinCondition} ";
                }
            }

            // Build the WHERE clause
            $sql .= "WHERE ";

            // Handle soft delete
            if (!$showDeleted) {
                $conditions[] = [$joined ? "{$mainTableAlias}.{$softDeleteColumn}" : $softDeleteColumn, 'IS', 'NULL'];
            }

            $lastIndex = count($conditions) - 1;
            foreach ($conditions as $index => $condition) {

                if (is_array($condition) && count($condition) >= 3 && in_array($condition[1], $operators)) {

                    if(!empty($condition[3]) && in_array(strtoupper($condition[3]), ['AND', 'OR']) && $index !== $lastIndex){
                        $logicalOperator = strtoupper($condition[3]);
                    }else{
                        $logicalOperator = 'AND';
                    }

                    switch ($condition[1]) {
                        case 'IS':
                            if(in_array($condition[2],$allowedIsValues)){
                                $sql .= "$condition[0] $condition[1] $condition[2] $logicalOperator ";

                            }else{
                                throw new Error("Invalid IS operator value {$condition[2]}");
                            }
                            
                            break;


                        case 'NOT IN':
                        case 'IN':

                            $placeholder = [];

                            foreach($condition[2] as $k => $v){
                               $key = "in_{$index}_{$k}";
                               $data[$key] = $v;
                               $placeholder[] = ":$key";
                            }

                            $sql .= "{$condition[0]} {$condition[1]} (". implode(', ', $placeholder) . ") $logicalOperator ";

                            break;
                        
                        default:

                            $affectedCol = $condition[0];

                            if ($joined){
                                $result = explode('.', $condition[0]);
                                if (count($result) == 2){
                                    $affectedCol = $result[0] . '_' . $result[1];
                                }
                            }

                            $sql .= "{$condition[0]} {$condition[1]} :{$affectedCol} $logicalOperator ";
                            $data[$affectedCol] = $condition[2];
                            break;
                    }

                } else {

                    // Handle invalid condition format
                    error_log("Invalid condition format: " . json_encode($condition));
                }
            
            }

            // Handle the trailing AND in the previous loop
            $sql .= "TRUE ";

            // Build the GROUP BY clause (simple: accepts alias.column or array of fields)
            if (!empty($groupBy)) {
                if (!is_array($groupBy)) {
                    $groupBy = [$groupBy];
                }

                $sql .= " GROUP BY " . implode(', ', $groupBy) . " ";
            }

            // Build the ORDER BY clause
            foreach ($orderBy as $field => $direction) {
                if (!in_array(strtoupper($direction), ['ASC', 'DESC'])) {
                    error_log("Invalid ORDER BY direction: $direction for field: $field");
                }

                $sql .= " ORDER BY $field $direction ";
            }
            
            $sql .= $limit ? " LIMIT $limit " : "";
            $sql .= $offset ? " OFFSET $offset " : "";
            
            return $this->query($sql, $data);
        } catch (PDOException $e) {
            die("WHERE query failed: " . $e->getMessage());
        }
    }

    public function first($data)
    {
        try {
            $keys = array_keys($data);
            $conditions = implode(" AND ", array_map(fn($key) => "$key = :$key", $keys));
            $sql = "SELECT * FROM {$this->table} WHERE $conditions LIMIT 1";
            return $this->get_row($sql, $data);
        } catch (PDOException $e) {
            die("FIRST query failed: " . $e->getMessage());
        }
    }

    /**
     * Handles both single and multiple row inserts
     * @param array $columns simple array with column names ["col1", "col2", ...]
     * @param array $data a array of arrays with the column order maintained [[val1, val2, ...], [val1, val2, ...], ...]
     * Can be sent as [val1, val2, ...] for single row inserts
     */
    public function insert($columns, $data = [])
    {
        try {
            $sql = "INSERT INTO {$this->table} (". implode(", ", $columns) .") VALUES ";

            $passedData = [];            

            if(!is_array($data[0])){
                $data = [$data];
            }

            foreach($data as $i => $row){
                $placeholders = [];
                foreach($row as $j => $value){
                    $key = "{$columns[$j]}_{$i}";
                    $placeholders[] = ":$key";
                    $passedData[$key] = $value;
                }
                $sql .= "(". implode(", ", $placeholders) ."), ";
            }
            $sql = rtrim($sql, ", "); 

            return $this->query($sql, $passedData);
        } catch (PDOException $e) {
            die("INSERT failed: " . $e->getMessage());
        }
    }

    public function insertAndFetch($data)
    {
        try {
            $keys = array_keys($data);
            $columns = implode(", ", $keys);
            $placeholders = implode(", ", array_map(fn($key) => ":$key", $keys));
            $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders) RETURNING *";
            return $this->get_row($sql, $data);
        } catch (PDOException $e) {
            die("INSERT+FETCH failed: " . $e->getMessage());
        }
    }

    public function update($id, $data, $id_column = 'id')
    {
        try {
            $keys = array_keys($data);
            $set = implode(", ", array_map(fn($key) => "$key = :$key", $keys));
            $sql = "UPDATE {$this->table} SET $set WHERE $id_column = :id";
            $data['id'] = $id;
            return $this->query($sql, $data);
        } catch (PDOException $e) {
            die("UPDATE failed: " . $e->getMessage());
        }
    }

    // For decrementing/incrementing numeric columns
    // Use negative $amount for decrementing
    public function increment($id, $column, $amount = 1, $id_column = 'id')
    {
        try {
            $sql = "UPDATE {$this->table} SET $column = $column + :amount WHERE $id_column = :id";
            $data = ['amount' => $amount, 'id' => $id];
            return $this->query($sql, $data);
        } catch (PDOException $e) {
            die("INCREMENT failed: " . $e->getMessage());
        }
    }

    /**
     * @param mixed $data Can be either an id value or an array of conditions in format [field ,operator, value]]
     * @param bool $softDelete If true, performs a soft delete by setting deleted_at timestamp; if false, performs a hard delete
     * NOTE: Soft delete requires a 'deleted_at' column in the table
     * NOTE: soft delete currently set to false by default to avoid breaking existing code. Change to true once usages are implemented properly
     */
    public function delete($conditionData, $softDelete = false)
    {
        $softDeleteColumn = $this->softDeleteColumn;        
        $defaultIdColumn = $this->defaultIdColumn;

        // Convenience method to delete by id
        if (!is_array($conditionData)){
            $conditionData = [[$defaultIdColumn, '=', $conditionData]];
        }

        try {
            $sql = "";

            if($softDelete){
                $sql = "UPDATE {$this->table} SET deleted_at = NOW() WHERE ";
            }else{
                $sql = "DELETE FROM {$this->table} WHERE ";
            }

            $data = [];

            foreach ($conditionData as $condition) {
                $sql .= "{$condition[0]} {$condition[1]} :{$condition[0]} AND ";
                $data[$condition[0]] = $condition[2];
            }

            $sql .= "TRUE ";

            return $this->query($sql, $data);
        } catch (PDOException $e) {
            die("DELETE failed: " . $e->getMessage());
        }
    }

    public function join($joinTable, $joinCondition, $type = 'INNER', $conditions = [], $limit = null, $offset = null, $orderBy = [])
    {
        try {
            $sql = "SELECT * FROM {$this->table} 
                {$type} JOIN {$joinTable} ON {$joinCondition}";


            $data = [];
            if (!empty($conditions)) {
                $sql .= " WHERE ";
                foreach ($conditions as $condition) {
                    $sql .= "{$condition[0]} {$condition[1]} :{$condition[0]} AND ";
                    $data[$condition[0]] = $condition[2];
                }
                $sql = rtrim($sql, "AND ");
            }


            foreach ($orderBy as $field => $direction) {
                $sql .= " ORDER BY $field $direction";
            }

            if ($limit) $sql .= " LIMIT $limit";
            if ($offset) $sql .= " OFFSET $offset";

            return $this->query($sql, $data);
        } catch (PDOException $e) {
            die("JOIN query failed: " . $e->getMessage());
        }
    }

    public function count($conditions = []){
        try {
            $selected = [["COUNT(*)", "count"]];
            
            $result = $this->where(
                conditions: $conditions,
                selected: $selected
            );
            return $result[0]->count ?? 0;
        } catch (PDOException $e) {
            die("COUNT query failed: " . $e->getMessage());
        }
    }
}
