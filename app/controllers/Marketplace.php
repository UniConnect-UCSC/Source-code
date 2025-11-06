<?php
require_once(__DIR__ . "/../models/MarketplaceItem.php");
require_once(__DIR__ . "/../models/MarketplaceItemImage.php");
require_once(__DIR__ . "/../models/ItemCategory.php");

require_once __DIR__ . '/../core/functions.php';

class Marketplace extends Controller
{
    public function index()
    {
        $allitemsModel = new MarketplaceItem();

        $this->view('marketplace', [
            'title' => 'Marketplace | UniConnect',
            'head' => '
            <link rel="stylesheet" href="/assets/css/pages/marketplace.css">
            <link rel="stylesheet" href="/assets/css/pages/home.css">
            <link rel="stylesheet" href="/assets/css/components/navbar.css">
            <link rel="stylesheet" href="/assets/css/components/navPanel.css">
            <link rel="stylesheet" href="/assets/css/components/createPost.css">
            <link rel="stylesheet" href="/assets/css/components/marketplaceFeed.css">
            <link rel="stylesheet" href="/assets/css/components/marketplaceCard.css">
            ',
        ]);
    }

    public function myItems()
    {

        $this->view('myItems', [
            'title' => 'My Items | UniConnect',
            'head' => '
            <link rel="stylesheet" href="/assets/css/pages/marketplace.css">
            <link rel="stylesheet" href="/assets/css/pages/home.css">
            <link rel="stylesheet" href="/assets/css/components/feed.css">
            <link rel="stylesheet" href="/assets/css/components/navbar.css">
            <link rel="stylesheet" href="/assets/css/components/navPanel.css">
            <link rel="stylesheet" href="/assets/css/components/marketplaceItem.css">
            <link rel="stylesheet" href="/assets/css/components/myItems.css">
            <link rel="stylesheet" href="/assets/css/components/marketplaceCard.css">
            ',
        ]);
    }
    public function createItem()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');

            try {

                $itemModel = new MarketplaceItem();
                $imageModel = new MarketplaceItemImage();

                $mediaURL = null;

                // Handle media upload to Cloudinary
                if (!empty($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
                    $tmpPath = $_FILES['media']['tmp_name'];

                    // Check if function exists
                    if (!function_exists('uploadImageToCloudinary')) {
                        throw new Exception('Image upload function not available');
                    }

                    $uploadedUrl = uploadImageToCloudinary($tmpPath, 'uniconnect_marketplace');

                    if ($uploadedUrl) {
                        $mediaURL = $uploadedUrl;
                    } else {
                        error_log('Cloudinary upload failed for user ' . $_SESSION['user_id']);
                        throw new Exception('Image upload failed');
                    }
                } else {
                    if (!empty($_FILES['media'])) {
                        error_log('File upload error code: ' . $_FILES['media']['error']);
                    }
                    throw new Exception('No image uploaded or upload error occurred');
                }

                // Create item data array
                $itemData = [
                    'title' => trim($_POST['title']),
                    'description' => trim($_POST['description']),
                    'price' => floatval($_POST['price']),
                    'category_id' => intval($_POST['category_id']),
                    'student_id' => $_SESSION['user_id'],
                    'status' => 'available',
                    'created_at' => date('Y-m-d H:i:s')
                ];

                // Insert item and get the created item
                $itemId = $itemModel->insertAndFetch($itemData);

                if (!$itemId) {
                    throw new Exception('Failed to create item in database');
                }

                // Insert image if we have a URL
                if ($mediaURL) {
                    $imageData = [
                        'image_url' => $mediaURL,
                        'marketplace_item_id' => $itemId->id
                    ];
                    $imageResult = $imageModel->insert($imageData);

                    if (!$imageResult) {
                        error_log('Failed to insert image for item ' . $itemId->id);
                    }
                }

                echo json_encode([
                    'success' => true,
                    'item_id' => $itemId,
                    'media_url' => $mediaURL,
                    'message' => 'Item created successfully'
                ]);
            } catch (Exception $e) {
                error_log("Marketplace::createItem error: " . $e->getMessage());
                error_log("Stack trace: " . $e->getTraceAsString());
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            exit;
        } else {
            header('Location: /marketplace/sellItem');
            exit;
        }
    }

    public function editItem($itemId)
    {
        $itemModel = new MarketplaceItem();
        $item = $itemModel->getitemsById($itemId);
        $statusOptions = $itemModel->getStatusOptions();

        if (!$item) {
            echo "Item not found";
            header('Location: /marketplace');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $itemData = [
                'id' => $itemId,
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'price' => $_POST['price'],
                'updated_at' => date('Y-m-d H:i:s'),
                'status' => $_POST['status']
            ];
            $itemModel->updateItem($itemData);
            header('Location: /marketplace/myItems');
            exit();
        }

        // Render the edit form on GET
        $this->view('editItem', [
            'title' => 'Edit Item - UniConnect',
            'head' => '
                <link rel="stylesheet" href="/assets/css/pages/marketplace.css">
                <link rel="stylesheet" href="/assets/css/pages/home.css">
                <link rel="stylesheet" href="/assets/css/components/feed.css">
                <link rel="stylesheet" href="/assets/css/components/navbar.css">
                <link rel="stylesheet" href="/assets/css/components/navPanel.css">
                <link rel="stylesheet" href="/assets/css/components/marketplaceItem.css">
                <link rel="stylesheet" href="/assets/css/components/widgetPanel.css">
                <link rel="stylesheet" href="/assets/css/components/eventsWidget.css">
                <link rel="stylesheet" href="/assets/css/components/createPost.css">
            ',
            'item' => $item,
            'statusOptions' => $statusOptions
        ]);
    }
    public function deleteItem($itemId)
    {
        $itemModel = new MarketplaceItem();
        $itemModel->deleteItem($itemId);
        header('Location: /marketplace/myItems');
        exit();
    }
}
