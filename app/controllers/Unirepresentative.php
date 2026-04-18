<?php

require_once(__DIR__ . "/../core/functions.php");

class UniRepresentative extends Controller{

    private $possibleStatuses = ['is_rep', 'has_rep', 'pending_request', 'no_rep'];

    private function getCurrentStatus($userId, $universityId){
        require_once(__DIR__ . "/../models/Representative.php");
        $model = new UniversityRepresentative();
        $rep = $model->getRep($universityId);

        if($rep == $userId){
            return $this->possibleStatuses[0]; // is_rep
        }
         
        if($rep != null){
            return $this->possibleStatuses[1]; // has_rep
        }

        // Check if there's a pending request
        require_once(__DIR__ . "/../models/RepresentativeRequest.php");
        $requestModel = new RepresentativeRequest();
        $result = $requestModel->hasPendingRequest($userId);

        if($result){
            return $this->possibleStatuses[2]; // pending_request
        }

        return $this->possibleStatuses[3]; // no_rep
    }

    public function apply(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = parseRequestData();  

            $userId = $_SESSION['user_id'];
            $universityId = $_SESSION['user_universityID'];

            $return = $this->getCurrentStatus($userId, $universityId);
            $returnMsg = "";
            switch($return){
                case $this->possibleStatuses[0]: // is_rep
                    $returnMsg = "You are already the uni rep";
                    break;
                case $this->possibleStatuses[1]: // has_rep
                    $returnMsg = "You are not the uni rep";
                    break;
                case $this->possibleStatuses[2]: // pending_request
                    $returnMsg = "You have a pending request";
                    break;
                case $this->possibleStatuses[3]: // no_rep
                    break;
            }

            if($returnMsg != ""){
                echo json_encode([
                    "success" => false,
                    "message" => $returnMsg
                ]);
                return;
            }

            //Upload proof to cloudinary
            $fileUrl = uploadImageToCloudinary($data['FILES']['proof_pdf'] ?? null, 'university_representative_proof');
            if(!$fileUrl){
                echo json_encode([
                    "success" => false,
                    "message" => "File upload failed. Please try again."
                ]);
                return;
            }

            //Save request to database
            require_once(__DIR__ . "/../models/RepresentativeRequest.php");
            $requestModel = new RepresentativeRequest();
            $response = $requestModel->createRequest($userId, $universityId, $fileUrl);

            if($response){
                header("Location: /unirepresentative");
                exit();
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "Failed to submit your application. Please try again."
                ]);
            }


        }
    }

    public function stepdown(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'];
            $universityId = $_SESSION['user_universityID'];

            $return = $this->getCurrentStatus($userId, $universityId);
            switch($return){
                case $this->possibleStatuses[0]: // is_rep
                    break;
                case $this->possibleStatuses[1]: // has_rep
                case $this->possibleStatuses[2]: // pending_request
                case $this->possibleStatuses[3]: // no_rep
                    echo json_encode([
                        "success" => false,
                        "message" => "You are not the current university representative."
                    ]);
                    return;
            }

            error_log("User $userId is stepping down as university representative for university $universityId");

            require_once(__DIR__ . "/../models/Representative.php");
            $model = new UniversityRepresentative();
            $return = $model->stepDown($userId);

            if($return){
                header("Location: /unirepresentative");
                exit();
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "Failed to step down. Please try again."
                ]);
            }
        }
    }

    public function index(){

        $userId = $_SESSION['user_id'];
        $universityId = $_SESSION['user_universityID'];

        $status = $this->getCurrentStatus($userId, $universityId);

		$this->view('unirepresentative', [
			'title' => 'University Representative • UniConnect',
			'head' => '
			<link rel="stylesheet" href="/assets/css/pages/home.css">
			<link rel="stylesheet" href="/assets/css/pages/university_representative.css">
			<link rel="stylesheet" href="/assets/css/components/feed.css">
			<link rel="stylesheet" href="/assets/css/components/modal.css">
			<link rel="stylesheet" href="/assets/css/components/navbar.css">
			<link rel="stylesheet" href="/assets/css/components/navPanel.css">
			<link rel="stylesheet" href="/assets/css/components/widgetPanel.css">
			<link rel="stylesheet" href="/assets/css/components/eventsWidget.css">
			',
            'status' => $status
		], );
    }
}