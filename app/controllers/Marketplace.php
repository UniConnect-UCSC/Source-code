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

                // Handle media upload to Cloudinary
                $mediaURL = uploadImageToCloudinary($tmpPath, 'uniconnect_marketplace');

                if(!$mediaURL){
                    throw new Exception('Failed to upload file');
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

    public function editItem()
    {
        $itemId = $_POST['item_id'];
        $itemTitle = $_POST['title'];
        $itemDescription = $_POST['description'];
        $itemPrice = $_POST['price'];
        $itemCategoryID = intval($_POST['category_id']);
        // $itemStatus = $_POST['status'];

        $validStatuses = ['available', 'sold', 'reserved'];
        $status = strtolower($_POST['status'] ?? 'available');
        if (!in_array($status, $validStatuses)) {
            throw new Exception('Invalid status value');
        }


        $itemModel = new MarketplaceItem();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $itemData = [
                'title' => $itemTitle,
                'description' => $itemDescription,
                'price' => $itemPrice,
                'updated_at' => date('Y-m-d H:i:s'),
                'status' => $status,
                // 'category_id' => $itemCategoryID,
            ];

            $mediaUrl = uploadImageToCloudinary($tmpPath, 'uniconnect_marketplace');

            $itemModel->update($itemId, $itemData);

            $imageModel = new MarketplaceItemImage();
            $existingImage = $imageModel->first(['marketplace_item_id' => $itemId]);

            $imageData = [
                'image_url' => $mediaUrl,
            ];
            if ($existingImage && $mediaUrl) {
                $imageModel->update($existingImage->id, $imageData);
            }

            header('Location: /marketplace/myItems');
            exit();
        }
    }
    public function deleteItem()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $itemId = $_POST['delete_item_id'] ?? null;
        if (!$itemId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'No item ID provided']);
            exit;
        }

        try {
            $itemModel = new MarketplaceItem();

            $item = $itemModel->first(['id' => $itemId]);
            if (!$item) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Item not found']);
                exit;
            }

            // delete related images if applicable
            $imageModel = new MarketplaceItemImage();
            if (method_exists($imageModel, 'delete')) {
                $imageModel->delete($itemId, 'marketplace_item_id');
            }

            $result = $itemModel->delete($itemId, 'id');

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Item deleted']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to delete item']);
            }
        } catch (Exception $e) {
            error_log("Marketplace::deleteItem error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    // public function editItem() {}
}