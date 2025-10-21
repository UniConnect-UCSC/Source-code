<?php
require_once(__DIR__."/../models/Kuppi.php");
require_once(__DIR__."/../models/KuppiCategory.php");
require_once(__DIR__."/../models/User.php");
class Kuppi extends Controller
{
    public function index(){
        $kuppi = new KuppiModel();
        $Kuppis = $kuppi->getKuppi();
        $KuppiCategory = new KuppiCategoryModel();
        $KuppiCategories = $KuppiCategory->getAllKuppiCategories();
    //    $universitymodel = new University();

    /*    foreach ($Kuppis as $kuppi) {
            $university_id = $kuppi->university_id;
            $kuppi->university_name = $universitymodel->getUniversityName($university_id);

        }*/
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
                'university' => $_SESSION['user_universityID'] ?? 'Unknown University'
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

    public function create_kuppi(){
        $KuppiCategory = new KuppiCategoryModel();
        $KuppiCategories = $KuppiCategory->getAllKuppiCategories();

        $this->view('create_kuppi', [
            'title' => 'Create Kuppi',
            'head' => '
            <link rel="stylesheet" href="/assets/css/components/navbar.css">
            <link rel="stylesheet" href="/assets/css/pages/create_kuppi.css">
            <link rel="stylesheet" href="/assets/css/pages/home.css"> 
            <link rel="stylesheet" href="/assets/css/components/kuppiPost.css">
            <link rel="stylesheet" href="/assets/css/components/feed.css">
            <link rel="stylesheet" href="/assets/css/components/navpanel.css">
            <link rel="stylesheet" href="/assets/css/components/widgetPanel.css">
            <link rel="stylesheet" href="/assets/css/components/eventsWidget.css">            '
            ,
            'kuppiCategories' => $KuppiCategories
        ]);
    }
    public function request_kuppi(){
        $KuppiCategory = new KuppiCategoryModel();
        $KuppiCategories = $KuppiCategory->getAllKuppiCategories();

        $this->view('request_kuppi', [
            'title' => 'Request Kuppi',
            'head' => '
            <link rel="stylesheet" href="/assets/css/components/navbar.css">
            <link rel="stylesheet" href="/assets/css/pages/request_kuppi.css">
            <link rel="stylesheet" href="/assets/css/components/navpanel.css">
            <link rel="stylesheet" href="/assets/css/components/widgetPanel.css">
            <link rel="stylesheet" href="/assets/css/components/eventsWidget.css">
            '
        ]);
    }
    public function edit_kuppi($id){
        $kuppiModel = new KuppiModel();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $topic = $_POST['topic'] ?? '';
            $date = $_POST['date'] ?? '';
            $time = $_POST['time'] ?? '';
            $platform = $_POST['platform'] ?? '';
            $category_id = $_POST['category_id'] ?? '';
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
            ',
            'myKuppies' => $mykuppies
        ]);
    }
}