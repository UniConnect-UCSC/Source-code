<?php
require_once(__DIR__."/../models/Kuppi.php");
require_once(__DIR__."/../models/KuppiCategory.php");
require_once(__DIR__."/../models/User.php");
require_once(__DIR__."/../models/University.php");

class Kuppi extends Controller
{
    public function index(){
        $usermodel = new User();
        $kuppi = new KuppiModel();
        $Kuppis = $kuppi->getKuppi();
        $KuppiCategorymodel = new KuppiCategoryModel();
        $KuppiCategories = $KuppiCategorymodel->getAllKuppiCategories();
        $universitymodel = new University();
  error_log('user_universityID: ' . print_r($_SESSION['user_universityID'], true));
       foreach ($Kuppis as $kuppi) {
            $host_name = $usermodel->getUserNameById($kuppi->host_id);
            $kuppi->host_name = $host_name;
            $university_id = $kuppi->university_id;
            $kuppi->university = $universitymodel->getUniversityName($university_id);
            $categoryObj = $KuppiCategorymodel->getKuppiCategoryById($kuppi->category_id);
            $kuppi->category = $categoryObj ? $categoryObj->category_name : '';
        }

 //changes to be made : university id to university name

        $this->view('kuppi', [
            'title' => 'UniConnect',
            'head' => '
            <link rel="stylesheet" href="/assets/css/pages/kuppi.css">
            <link rel="stylesheet" href="/assets/css/pages/home.css">
            <link rel="stylesheet" href="/assets/css/components/navbar.css">
            <link rel="stylesheet" href="/assets/css/components/navPanel.css">
            <link rel="stylesheet" href="/assets/css/components/feed.css">
            <link rel="stylesheet" href="/assets/css/components/widgetPanel.css">
            <link rel="stylesheet" href="/assets/css/components/kuppiPost.css">
            <link rel="stylesheet" href="/assets/css/components/eventsWidget.css">
            ',
            'testKuppi' => $Kuppis,
            'kuppiCategories' => $KuppiCategories
        ]);
    }
    public  function create(){
        $user = new User();
        $university_id = $_SESSION['user_universityID'] ?? null;
          error_log('user_universityID: ' . print_r($_SESSION, true));

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Retrieve form data
            $topic = $_POST['topic'] ?? '';
            $date = $_POST['date'] ?? '';
            $time = $_POST['time'] ?? '';
            $platform = $_POST['platform'] ?? '';
            $category_id = $_POST['category_id'] ?? '';

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
                'status' => 'In Progress'
            
                // Add other necessary fields like university_id, image_url, etc.
            ];
            error_log('user_universityID: ' . print_r($_SESSION['user_universityID'], true));

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

            $data = [
                'topic' => $topic,
                'kuppi_date_time' => $kuppiDateTime,
                'platform' => $platform,
                'category_id' => $category_id,
            ];

            $updated = $kuppiModel->update($id, $data);

            if ($updated) {
                header('Location: /kuppi');
                exit();
            } else {
                echo "Error updating Kuppi session.";
            }
        } else {
            $kuppi = $kuppiModel->getKuppiById($id);
            $KuppiCategory = new KuppiCategoryModel();
            $KuppiCategories = $KuppiCategory->getAllKuppiCategories();

            $this->view('edit_kuppi', [
                'title' => 'Edit Kuppi',
                'kuppi' => $kuppi,
                'kuppiCategories' => $KuppiCategories
            ]);
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
    public function my_kuppis(){
        $kuppi = new KuppiModel();
        $mykuppies = $kuppi->getMyKuppies($_SESSION['user_id']);
        $kuppiCategoryModel = new KuppiCategoryModel();
        $universitymodel = new University();
        foreach ($mykuppies as $kuppi) {
            $categoryObj = $kuppiCategoryModel->getKuppiCategoryById($kuppi->category_id);
            $kuppi->category = $categoryObj ? $categoryObj->category_name : '';
            $university_id = $kuppi->university_id;
            $kuppi->university = $universitymodel->getUniversityName($university_id);
        }
        $this->view('my_kuppis', [
            'title' => 'My Kuppis',
            'head' => '
            <link rel="stylesheet" href="/assets/css/pages/kuppi.css">
            <link rel="stylesheet" href="/assets/css/pages/home.css">
            <link rel="stylesheet" href="/assets/css/components/navbar.css">
            <link rel="stylesheet" href="/assets/css/components/navPanel.css">
            <link rel="stylesheet" href="/assets/css/components/feed.css">
            <link rel="stylesheet" href="/assets/css/components/widgetPanel.css">
            <link rel="stylesheet" href="/assets/css/components/kuppiPost.css">
            <link rel="stylesheet" href="/assets/css/components/eventsWidget.css">
            <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
            ',
            'myKuppies' => $mykuppies
        ]);
    }
    public function kuppi_requests(){
        $usermodel = new User();
        $kuppiModel = new KuppiModel();
        $kuppiRequests = $kuppiModel->getKuppiRequests();
        $KuppiCategorymodel = new KuppiCategoryModel();
        $KuppiCategories = $KuppiCategorymodel->getAllKuppiCategories();

        foreach ($kuppiRequests as $kuppi) {
            $kuppi->requester_name = (new User())->getUserNameById($kuppi->requester_id);
            $categoryObj = $KuppiCategorymodel->getKuppiCategoryById($kuppi->category_id);
            $kuppi->category = $categoryObj ? $categoryObj->category_name : '';
            $kuppi->requester_university = $usermodel->getUserUniversityNameById($kuppi->requester_id);
        }

        $this->view('kuppi_requests', [
            'title' => 'Kuppi Requests',
            'head' => '

            <link rel="stylesheet" href="/assets/css/pages/home.css">
            <link rel="stylesheet" href="/assets/css/pages/kuppi.css">
            <link rel="stylesheet" href="/assets/css/components/feed.css">
            <link rel="stylesheet" href="/assets/css/components/kuppiPost.css">
            <link rel="stylesheet" href="/assets/css/components/navbar.css">
            <link rel="stylesheet" href="/assets/css/components/navPanel.css">
            <link rel="stylesheet" href="/assets/css/components/widgetPanel.css">
            <link rel="stylesheet" href="/assets/css/components/eventsWidget.css">
            <link rel="stylesheet" href="/assets/css/pages/kuppi_requests.css">

            ',
            'kuppiRequests' => $kuppiRequests,
            'kuppiCategories' => $KuppiCategories
        ]);
    
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
}