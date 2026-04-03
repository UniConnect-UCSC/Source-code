<?php

require_once(__DIR__ . '/../recipients/userRecipient.php'); 

/**
 * This Provider is Very non-derministic provider 
 * Prefer using this for everything as this is much more optimized for client side and will not cause memory issues
 * AVOID!! using this from notifications sent on data that's been deleted afterwards (event deletion notice for notifying participating users)
 * use the deterministic provider for that
 * */
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
        error_log("[TempModel] fetchNextBatch called. hasMoreRecords: " . ($this->hasMoreRecords ? 'true' : 'false'));

        if(!$this->hasMoreRecords){
            error_log("[TempModel] No more records to fetch.");
            return [];
        }

        $this->whereConstructorInputs['limit'] = $this->perLimit;
        $this->whereConstructorInputs['offset'] = $this->batchSize;
        error_log("[TempModel] whereConstructorInputs: " . print_r($this->whereConstructorInputs, true));

        try {
            $response = $this->where(...$this->whereConstructorInputs);
            error_log("[TempModel] where() response: " . print_r($response, true));
        } catch (Throwable $e) {
            error_log("[TempModel] Exception in where(): " . $e->getMessage());
            $this->hasMoreRecords = false;
            return [];
        }

        if ($response === false || empty($response) || !is_array($response)) {
            error_log("[TempModel] No results or error from where().");
            $this->hasMoreRecords = false;
            return [];
        }

        if(count($response) < $this->perLimit){
            error_log("[TempModel] Fetched less than perLimit, marking as last batch.");
            $this->hasMoreRecords = false;
        }

        $this->batchSize += $this->perLimit;
        error_log("[TempModel] Returning batch of " . count($response) . " rows.");

        return $response;
    }
}

