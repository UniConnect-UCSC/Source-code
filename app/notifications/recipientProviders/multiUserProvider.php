<?php

require_once(__DIR__ . '/../recipients/userRecipient.php'); 

class multiUserProvider implements RecipientProviderInterface {
    private string $idColumnName;
    private array $whereConstructorInputs;
    private string $tableName;
    private int $batchSize = 10; // Number of recipients to fetch per batch

    private $constructorInput;

    
    public function __construct(string $idColumnName,string $tableName, array $whereConstructorInputs){
        $this->idColumnName = $idColumnName;
        $this->tableName = $tableName;
        $this->whereConstructorInputs = $whereConstructorInputs;

        $this->setConstructorInputs();
    }

    private function setConstructorInputs(): void {
        $this->constructorInput['idColumnName'] = $this->idColumnName;
        $this->constructorInput['tableName'] = $this->tableName;
        $this->constructorInput['whereConstructorInputs'] = $this->whereConstructorInputs;
    }

    public function getRecipients(): iterable {

        // This will replace if a selected was already set (For safety)
        $this->whereConstructorInputs['selected'] = [
            $this->idColumnName
        ];

        // Initialize a temporary model to fetch user IDs in batches
        $tempModel = new TempModel($this->tableName, $this->batchSize, $this->whereConstructorInputs);

        while ($rows = $tempModel->fetchNextBatch()) {
            foreach ($rows as $row) {
                yield new userRecipient($row->{$this->idColumnName});
            }
        }
    }
    
    public function getConstructorInputs(): array {
        return $this->constructorInput;
    }
}

class TempModel {
    use Model;
    protected string $table = ""; 
    private array $whereConstructorInputs;
    private int $perLimit;
    private int $batchSize = 0;
    private bool $hasMoreRecords = true;

    public function __construct(string $tableName, int $limit, array $whereConstructorInputs){
        $this->table = $tableName;
        $this->perLimit = $limit;
        $this->whereConstructorInputs = $whereConstructorInputs;
    }

    public function fetchNextBatch(){

        if(!$this->hasMoreRecords){
            return [];
        }

        $this->whereConstructorInputs['limit'] = $this->perLimit;
        $this->whereConstructorInputs['offset'] = $this->batchSize;

        $response = $this->where(...$this->whereConstructorInputs);

        if ($response === false || empty($response) || !is_array($response)) {
            $this->hasMoreRecords = false;
            return [];
        }

        if(count($response) < $this->perLimit){
            $this->hasMoreRecords = false;
        }

        $this->batchSize += $this->perLimit;

        return $response;
    }
}

