<?php 

class RepresentativeRequest
{
    use Model;
    protected $table = 'uni_rep_requests';

    public function hasPendingRequest($userId){
        $result = $this->first(['student_id' => $userId, 'status' => 'pending']);
        return $result ? true : false;
    }

    public function createRequest($userId, $universityId, $proofUrl){
        $response = $this->insert(
            ['student_id', 'university_id', 'proof_url'],
            [$userId, $universityId, $proofUrl]
        );

        return $response ? true : false;
    }
}