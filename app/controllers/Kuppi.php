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
        $university_id = $user->getUniId($_SESSION['user_id']);
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
                'university_id' => $university_id// Placeholder requester ID, replace with actual user ID
                // Add other necessary fields like university_id, image_url, etc.
            ];

            // Insert the new Kuppi session into the database
            $insertedId = $kuppiModel->insert($data);

            if ($insertedId) {
                // Redirect to the Kuppi page or show a success message
                echo "Kuppi session created successfully!";
                header('Location: /kuppi');
                exit();
            } else {
                // Handle insertion failure (e.g., show an error message)
                echo "Error creating Kuppi session.";
            }
        } else {
            // If not a POST request, redirect to the Kuppi page
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
            // Get updated data from form
            $topic = $_POST['topic'] ?? '';
            $date = $_POST['date'] ?? '';
            $time = $_POST['time'] ?? '';
            $platform = $_POST['platform'] ?? '';
            $category_id = $_POST['category_id'] ?? '';
            $kuppiDateTime = $date . ' ' . $time;

            // Prepare data for update
            $data = [
                'topic' => $topic,
                'kuppi_date_time' => $kuppiDateTime,
                'platform' => $platform,
                'category_id' => $category_id,
            ];

            // Update the Kuppi session
            $updated = $kuppiModel->update($id, $data);

            if ($updated) {
                header('Location: /kuppi');
                exit();
            } else {
                echo "Error updating Kuppi session.";
            }
        } else {
            // GET: Show the edit form
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