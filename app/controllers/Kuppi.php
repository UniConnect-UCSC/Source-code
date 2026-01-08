<?php
require_once(__DIR__."/../models/Kuppi.php");
require_once(__DIR__."/../models/KuppiCategory.php");
require_once(__DIR__."/../models/User.php");
require_once(__DIR__."/../models/University.php");
require_once(__DIR__."/../core/functions.php");

class Kuppi extends Controller
{
    public function index(){
        // Only load categories; posts will be fetched dynamically via AJAX
        $KuppiCategorymodel = new KuppiCategoryModel();
        $KuppiCategories = $KuppiCategorymodel->getAllKuppiCategories();
        $this->view('kuppi', [
            'title' => 'UniConnect',
            'head' => '
            <link rel="stylesheet" href="/assets/css/pages/kuppi.css">
            <link rel="stylesheet" href="/assets/css/pages/home.css">
            <link rel="stylesheet" href="/assets/css/components/navbar.css">
            <link rel="stylesheet" href="/assets/css/components/navPanel.css">
            <link rel="stylesheet" href="/assets/css/components/feed.css">
            <link rel="stylesheet" href="/assets/css/components/widgetPanel.css">
            <link rel="stylesheet" href="/assets/css/components/kuppi/kuppiPost.css">
            <link rel="stylesheet" href="/assets/css/components/eventsWidget.css">
            ',
            'kuppiCategories' => $KuppiCategories
        ]);
    }
    // JSON endpoint: return paginated Kuppi posts enriched with names
    private function fetchKuppis($offset, $limit){
        
        
        $kuppiModel = new KuppiModel();
        $kuppies = $kuppiModel->getKuppi($offset, $limit) ?? [];

        foreach ($kuppies as $item){
            $item->host_name = $item->host_f_name." ".$item->host_l_name;
            $item->requester_name = $item->requester_f_name." ".$item->requester_l_name;
        }

        return $kuppies;
    }


    public  function create(){
        $user = new User();
        $university_id = $_SESSION['user_universityID'] ?? null;

        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Retrieve form data
            $topic = $_POST['topic'] ?? '';
            $date = $_POST['date'] ?? '';
            $time = $_POST['time'] ?? '';
            $platform = $_POST['platform'] ?? '';
            $category_id = $_POST['category_id'] ?? '';
            $link = $_POST['link'] ?? '';
            
            // Validate and sanitize input as needed
            
            // Combine date and time into a single datetime string
            $kuppiDateTime = $date . ' ' . $time;
            
            // Create a new KuppiModel instance
            $kuppiModel = new KuppiModel();
            
            // Prepare data for insertion
            $data = [
                'topic' => $topic,
                'kuppi_date_time' => $kuppiDateTime,
                'platform' => $platform,
                'image_url' => 'assets/images/ml-banner.jpg', // Placeholder image URL
                'host_id' => $_SESSION['user_id'],
                'category_id' => $category_id,
                'university_id' => $_SESSION['user_universityID'],
                'status' => 'In Progress',
                'kuppi_url' => $link,
                
                // Add other necessary fields like university_id, image_url, etc.
            ];
            
            // Insert the new Kuppi session into the database
            $insertedId = $kuppiModel->insert($data);
            
            if ($insertedId) {
                echo "Kuppi session created successfully!";
                header('Location: /kuppi');
                exit();
            } else {
                echo "Error creating Kuppi session.";
            }
        } else {
            header('Location: /kuppi');
            exit();
        }
    }
    
    public function request_kuppi(){
        $kuppiModel = new KuppiModel();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $topic = $_POST['topic'];
            $requester_id = $_SESSION['user_id'];
            $category_id = $_POST['category_id'] ?? '';
            
            $data = [
                'topic' => $topic,
                'category_id' => $category_id,
                'requester_id' => $requester_id,    
                'host_id' => null, // No host for requested Kuppi
                'university_id' => $_SESSION['user_universityID'],
                'status' => 'Requested' // Initial status
            ];
            
            $insertedId = $kuppiModel->insert($data);
            
            if ($insertedId) {
                echo "Kuppi request submitted successfully!";
                header('Location: /kuppi');
                exit();
            } else {
                echo "Error submitting Kuppi request.";
            }
        } else {
            header('Location: /kuppi');
            exit();
        }
    }
    
    public function edit_kuppi($id){
        $kuppiModel = new KuppiModel();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $topic = $_POST['topic'] ?? '';
            $date = $_POST['date'] ?? '';
            $time = $_POST['time'] ?? '';
            $platform = $_POST['platform'] ?? '';
            if (!empty($category_id)) {
                $data['category_id'] = (int)$category_id;
            }
            $kuppiDateTime = $date . ' ' . $time;
            $link = $_POST['link'] ?? '';
            
            $data = [
                'topic' => $topic,
                'kuppi_date_time' => $kuppiDateTime,
                'platform' => $platform,
                'category_id' => $category_id,
                'kuppi_url' => $link
            ];
            
            $updated = $kuppiModel->update($id, $data);
            
            if ($updated) {
                header('Location: /kuppi');
                exit();
            } else {
                echo "Error updating Kuppi session.";
            }
        } else {
            header('Location: /kuppi');
            console_error('Invalid request method for editing Kuppi.');
            exit();
        }
    }
    public function delete_kuppi($id){
        $kuppiModel = new KuppiModel();
        $deleted = $kuppiModel->delete($id);
        
        if ($deleted) {
            header('Location: /kuppi');
            exit();
        } else {
            echo "Error deleting Kuppi session.";
        }
    }
    public function fetchMyKuppies($offset, $limit){

        
        $kuppiModel = new KuppiModel();
        $KuppiCategorymodel = new KuppiCategoryModel();

        $myKuppies = $kuppiModel->getMyHosts($offset ,$limit ) ?? [];

        foreach ($myKuppies as $item){
            $item->host_name = $item->host_f_name." ".$item->host_l_name;

            if(isset($item->requester_f_name) || !empty($item->requester_f_name)){
                $item->requester_name = $item->requester_f_name." ".$item->requester_l_name;
            }
            $item->university = $item->host_university;
            if (!isset($item->image_url) || empty($item->image_url)) {
                 $item->image_url = 'assets/images/ml-banner.jpg';
            }
        }
        return $myKuppies;
    }


    public function fetchKuppiRequests($offset, $limit){

        
        $kuppiModel = new KuppiModel();
        $kuppiRequests = $kuppiModel->getKuppiRequests($offset, $limit);

        foreach ($kuppiRequests as $item) {
            $item->requester_name = $item->requester_f_name.' '.$item->requester_l_name;

            if (!isset($item->image_url) || empty($item->image_url)) {
                 $item->image_url = 'assets/images/ml-banner.jpg';
            }
        }
        return $kuppiRequests;
    }
    
    public function approve_request($id){
        $kuppiModel = new KuppiModel();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $platform = $_POST['platform'] ?? '';
            $date_time = $_POST['date'].' '.$_POST['time'] ?? '';
            $data = [
                'status' => 'In Progress',
                'university_id' => $_SESSION['user_universityID'],
                'platform' => $platform,
                'host_id' => $_SESSION['user_id'],
                'kuppi_date_time' => $date_time
            ];
        }
        $updated = $kuppiModel->update($id, $data);
        
        if ($updated) {
            header('Location: /kuppi/kuppi_requests');
            exit();
        } else {
            echo "Error approving Kuppi request.";
        }
    }
    public function editKuppiRequest(){
        $kuppiModel = new KuppiModel();
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $id = $_POST['id'] ?? '';
            $topic = $_POST['topic'] ?? '';
            $category_id = $_POST['category_id'] ?? '';
            
            $data = [
                'topic' => $topic,
                'category_id' => $category_id
            ];
            
            $updated = $kuppiModel->update($id, $data);
            
            if($updated){
                header('Location: /kuppi/myKuppis');
                exit();
            } else {
                echo "Error updating Kuppi request.";
            }
        } else {
            header('Location: /kuppi/myKuppis');
            exit();
        }
        
    }
    public function scrollable(){
        $data = parseRequestData();
        header('Content-Type: application/json');
    
        switch ($data['scrollIdentifier']) {
            case 'getAllKuppies':
                $response = $this->fetchKuppis($data['offset'], $data['limit']);
                echo json_encode($response);
                break;
            case 'getMyKuppies':
                $response = $this->fetchMyKuppies($data['offset'], $data['limit']);
                echo json_encode($response);
                break;
            case 'getKuppiRequests':
                $response = $this->fetchKuppiRequests($data['offset'], $data['limit']);
                echo json_encode($response);
                break;
            default:
                # code...
                break;
        }
    }
}