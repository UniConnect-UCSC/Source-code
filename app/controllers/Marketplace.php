<?php
require_once(__DIR__ . "/../models/MarketplaceItem.php");
require_once(__DIR__ . "/../models/MarketplaceItemImage.php");
require_once(__DIR__ . "/../models/ItemCategory.php");
require_once(__DIR__ . "/../models/MarketPlaceCategories.php");

require_once __DIR__ . '/../core/functions.php';


require_once(__DIR__ . "/../models/User.php");
require_once(__DIR__ . "/../models/MarketplaceSavedItem.php");


class Marketplace extends Controller
{
    private function collectUploadedImages($fieldNames = ['media', 'images', 'image'])
    {
        $files = [];

        foreach ($fieldNames as $fieldName) {
            if (!isset($_FILES[$fieldName])) {
                continue;
            }

            $input = $_FILES[$fieldName];

            if (is_array($input['name'])) {
                foreach ($input['name'] as $index => $name) {
                    if (($input['error'][$index] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                        continue;
                    }

                    $files[] = [
                        'name' => $name,
                        'type' => $input['type'][$index] ?? '',
                        'tmp_name' => $input['tmp_name'][$index] ?? '',
                        'error' => $input['error'][$index],
                        'size' => $input['size'][$index] ?? 0,
                    ];
                }

                continue;
            }

            if (($input['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                $files[] = $input;
            }
        }

        return $files;
    }

    private function uploadMarketplaceImages($uploadedFiles)
    {
        $uploadedImageUrls = [];

        foreach ($uploadedFiles as $file) {
            $imageURL = uploadImageToCloudinary($file, 'uniconnect_marketplace');
            if (!$imageURL) {
                throw new Exception('Failed to upload one or more images');
            }
            $uploadedImageUrls[] = $imageURL;
        }

        return $uploadedImageUrls;
    }

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
                $itemModel  = new MarketplaceItem();
                $imageModel = new MarketplaceItemImage();

                // Require logged in user
                $studentId = $_SESSION['user_id'] ?? null;
                if (!$studentId) {
                    http_response_code(401);
                    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                    exit;
                }

                $uploadedFiles = $this->collectUploadedImages();
                if (empty($uploadedFiles)) {
                    throw new Exception('Image is required');
                }

                if (count($uploadedFiles) > 5) {
                    throw new Exception('Maximum 5 images are allowed');
                }

                $imageUrls = $this->uploadMarketplaceImages($uploadedFiles);
                $primaryImageUrl = $imageUrls[0] ?? null;

                // Basic validation
                $title = trim($_POST['title'] ?? '');
                $description = trim($_POST['description'] ?? '');
                $price = floatval($_POST['price'] ?? 0);

                if ($title === '' || $description === '') {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Title and description are required']);
                    exit;
                }

                if ($price <= 0) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Price must be greater than 0']);
                    exit;
                }

                // Validate category_id (prevents FK error category_id=0)
                $categoryId = intval($_POST['category_id'] ?? 0);
                if ($categoryId <= 0) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Please select a valid category']);
                    exit;
                }

                $contact_number = trim($_POST['contact_number'] ?? '');
                if ($contact_number !== '') {
                    $contact_number = preg_replace('/\D+/', '', $contact_number);
                }   



                // Create item data array
                $itemData = [
                    'title'       => $title,
                    'description' => $description,
                    'price'       => $price,
                    'category_id' => $categoryId,
                    'student_id'  => $studentId,
                    'status'      => $status = $_POST['status'] ?? 'available',
                    'image_url'   => $primaryImageUrl,
                    'created_at'  => date('Y-m-d H:i:s'),
                    'updated_at'  => date('Y-m-d H:i:s'),
                    'contact_number' => $contact_number
                ];

                error_log("Creating marketplace item with data: " . print_r($itemData, true));

                // Insert item and get created row
                $item = $itemModel->insertAndFetch($itemData);
                if (!$item) {
                    throw new Exception('Failed to create item in database');
                }

                // Optional: also save into marketplace_item_images table (keeps existing design)
                foreach ($imageUrls as $imageUrl) {
                    $imageModel->insert(
                        ['image_url', 'marketplace_item_id'],
                        [$imageUrl,$item->id]
                    );
                }

                echo json_encode([
                    'success' => true,
                    'item_id' => $item->id,
                    'image_url' => $primaryImageUrl,
                    'image_urls' => $imageUrls,
                    'message' => 'Item created successfully'
                ]);
            } catch (Exception $e) {
                error_log("Marketplace::createItem error: " . $e->getMessage());
                error_log("Stack trace: " . $e->getTraceAsString());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;
        }

        header('Location: /marketplace');
        exit;
    }

    public function editItem($itemId = null)
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            header('Location: /login');
            exit;
        }

        if (!$itemId) {
            header('Location: /marketplace/myItems');
            exit;
        }

        $itemModel = new MarketplaceItem();

        // Fetch item + verify ownership (for both GET and POST)
        $item = $itemModel->first(['id' => $itemId]);
        if (!$item || ($item->student_id ?? null) !== $userId) {
            header('Location: /marketplace/myItems');
            exit;
        }

        $categoryModel = new itemcategorymodel();
        $categories = $categoryModel->getAllCategories();
        $imageModel = new MarketplaceItemImage();

        // POST: update
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $title = trim($_POST['title'] ?? '');
                $description = trim($_POST['description'] ?? '');
                $price = floatval($_POST['price'] ?? 0);
                $contactNumber = trim($_POST['contact_number'] ?? '');

                if ($title === '' || $description === '') {
                    throw new Exception('Title and description are required');
                }
                if ($price <= 0) {
                    throw new Exception('Price must be greater than 0');
                }



                // Validate status
                $status = strtolower(trim($_POST['status'] ?? 'available'));
                $validStatuses = $itemModel->getStatusOptions(); // ['available','sold','reserved']
                if (!in_array($status, $validStatuses, true)) {
                    $status = 'available';
                }

                $categoryId = intval($_POST['category_id'] ?? 0);
                if ($categoryId <= 0 || !$categoryModel->first(['id' => $categoryId])) {
                    throw new Exception('Please select a valid category');
                }

                $itemData = [
                    'title' => $title,
                    'description' => $description,
                    'price' => $price,
                    'status' => $status, // keep existing image unless updated
                    'contact_number' => $contactNumber,
                    'category_id' => $categoryId,
                    'updated_at' => date('Y-m-d H:i:s'),
                ];

                $result = $itemModel->update($itemId, $itemData, 'id');
                if (!$result) {
                    throw new Exception('Failed to update item');
                }

                $currentImages = $imageModel->getByItemId($itemId);
                $currentImageIds = array_map(function ($img) {
                    return (int)($img->id ?? 0);
                }, $currentImages);

                $removeImageIds = $_POST['remove_image_ids'] ?? [];
                if (!is_array($removeImageIds)) {
                    $removeImageIds = [];
                }

                $removeImageIds = array_values(array_unique(array_filter(array_map('intval', $removeImageIds), function ($id) {
                    return $id > 0;
                })));

                $removeImageIds = array_values(array_filter($removeImageIds, function ($id) use ($currentImageIds) {
                    return in_array($id, $currentImageIds, true);
                }));

                // Handle image upload if provided (accept media/images/image; single or multiple)
                $uploadedFiles = $this->collectUploadedImages();

                if (count($uploadedFiles) > 5) {
                    throw new Exception('Maximum 5 images are allowed');
                }

                $remainingCurrentCount = count($currentImages) - count($removeImageIds);
                $finalImageCount = $remainingCurrentCount + count($uploadedFiles);

                if ($finalImageCount < 1) {
                    throw new Exception('At least one image is required');
                }

                if ($finalImageCount > 5) {
                    throw new Exception('Maximum 5 images are allowed');
                }

                foreach ($removeImageIds as $removeImageId) {
                    $imageModel->deleteByIdForItem($removeImageId, $itemId);
                }

                if (!empty($uploadedFiles)) {
                    $newImageUrls = $this->uploadMarketplaceImages($uploadedFiles);

                    foreach ($newImageUrls as $newImageUrl) {
                        $imageModel->insert(
                            ['marketplace_item_id', 'image_url'],
                            [$itemId, $newImageUrl]
                        );
                    }

                }

                $updatedImages = $imageModel->getByItemId($itemId);
                $primaryImageUrl = !empty($updatedImages) ? ($updatedImages[0]->image_url ?? null) : null;

                $itemModel->update($itemId, [
                    'image_url' => $primaryImageUrl,
                    'updated_at' => date('Y-m-d H:i:s'),
                ], 'id');

                header('Location: /marketplace/myItems?success=updated');
                exit;
            } catch (Exception $e) {
                error_log("Marketplace::editItem error: " . $e->getMessage());
                header('Location: /marketplace/myItems?error=' . urlencode($e->getMessage()));
                exit;
            }
        }

        // GET: show edit form
        $statusOptions = $itemModel->getStatusOptions();

        $images = $imageModel->getByItemId($itemId);
        $currentImages = [];
        $imageUrls = [];
        foreach ($images as $image) {
            if (!empty($image->image_url)) {
                $currentImages[] = [
                    'id' => (int)($image->id ?? 0),
                    'url' => $image->image_url,
                ];
                $imageUrls[] = $image->image_url;
            }
        }

        $imageUrl = !empty($imageUrls) ? $imageUrls[0] : null;

        $this->view('editItem', [
            'title' => 'Edit Item | UniConnect',
            'item' => $item,
            'statusOptions' => $statusOptions,
            'imageUrl' => $imageUrl,
            'imageUrls' => $imageUrls,
            'currentImages' => $currentImages,
            'categories' => $categories,
            'contact_number' => $item->contact_number ?? '',
            'head' => '
            <link rel="stylesheet" href="/assets/css/pages/marketplace.css">
            <link rel="stylesheet" href="/assets/css/pages/home.css">
            <link rel="stylesheet" href="/assets/css/components/feed.css">
            <link rel="stylesheet" href="/assets/css/components/navbar.css">
            <link rel="stylesheet" href="/assets/css/components/navPanel.css">
            <link rel="stylesheet" href="/assets/css/components/editItem.css">
        ',
        ]);
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
            $imageModel = new MarketplaceItemImage();

            $item = $itemModel->first(['id' => $itemId]);
            if (!$item) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Item not found']);
                exit;
            }

            // Verify ownership (same as Boardings)
            if (($item->student_id ?? null) !== ($_SESSION['user_id'] ?? null)) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit;
            }

            // delete related images first
            if (method_exists($imageModel, 'delete')) {
                $imageModel->delete([['marketplace_item_id', '=', $itemId]]);
            }

            $result = $itemModel->delete([['id', '=', $itemId]]);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Item deleted successfully']);
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

    public function details($itemId = null)
    {
        if (!$itemId) {
            header('Location: /marketplace');
            exit;
        }

        $itemModel = new MarketplaceItem();
        $imageModel = new MarketplaceItemImage();
        $categoryModel = new MarketPlaceCategories();
        $userModel = new User();

        $item = $itemModel->first(['id' => $itemId]);
        if (!$item) {
            header('Location: /marketplace');
            exit;
        }

        $images = $imageModel->getByItemId($itemId);
        $imageUrls = [];
        foreach ($images as $image) {
            if (!empty($image->image_url)) {
                $imageUrls[] = $image->image_url;
            }
        }

        if (empty($imageUrls) && !empty($item->image_url)) {
            $imageUrls[] = $item->image_url;
        }

        $categoryName = null;
        if (!empty($item->category_id)) {
            $category = $categoryModel->first(['id' => $item->category_id]);
            $categoryName = $category->name ?? null;
        }

        $sellerName = null;
        $sellerContactNumber = null;

        $possibleContactFields = ['contact_number', 'phone', 'phone_number', 'mobile', 'mobile_number', 'telephone'];

        foreach ($possibleContactFields as $field) {
            if (!empty($item->$field)) {
                $sellerContactNumber = trim((string)$item->$field);
                break;
            }
        }

        if (!empty($item->student_id)) {
            $sellerName = $userModel->getUserNameById($item->student_id);

            if (!$sellerContactNumber) {
                $seller = $userModel->first(['id' => $item->student_id]);
                if ($seller) {
                    foreach ($possibleContactFields as $field) {
                        if (!empty($seller->$field)) {
                            $sellerContactNumber = trim((string)$seller->$field);
                            break;
                        }
                    }
                }
            }
        }

        $isOwner = isset($_SESSION['user_id']) && (string)$item->student_id === (string)$_SESSION['user_id'];

        $this->view('marketplaceDetails', [
            'title' => 'Item Details | UniConnect',
            'item' => $item,
            'imageUrls' => $imageUrls,
            'categoryName' => $categoryName,
            'sellerName' => $sellerName,
            'sellerContactNumber' => $sellerContactNumber,
            'isOwner' => $isOwner,
            'head' => '
            <link rel="stylesheet" href="/assets/css/pages/marketplace.css">
            <link rel="stylesheet" href="/assets/css/pages/home.css">
            <link rel="stylesheet" href="/assets/css/components/navbar.css">
            <link rel="stylesheet" href="/assets/css/components/navPanel.css">
            <link rel="stylesheet" href="/assets/css/components/marketplaceFeed.css">
            <link rel="stylesheet" href="/assets/css/components/marketplaceCard.css">
            <link rel="stylesheet" href="/assets/css/components/marketplaceDetails.css">
            ',
        ]);
    }

   
    public function getSavedItems()
    {
        header('Content-Type: application/json');

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $savedModel = new MarketplaceSavedItem();
        $items = $savedModel->listDetailedForUser($userId);

        echo json_encode(['success' => true, 'items' => $items]);
        exit;
    }

    public function saveItem()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $itemId = $_POST['item_id'] ?? null;
        if (!$itemId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing item_id']);
            exit;
        }

        $itemModel = new MarketplaceItem();
        $item = $itemModel->first(['id' => $itemId]);
        if (!$item) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Item not found']);
            exit;
        }

        $savedModel = new MarketplaceSavedItem();
        $row = $savedModel->addForUser($userId, $itemId);

        echo json_encode(['success' => true, 'saved' => true, 'row' => $row]);
        exit;
    }

    public function unsaveItem()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $itemId = $_POST['item_id'] ?? null;
        if (!$itemId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing item_id']);
            exit;
        }

        $savedModel = new MarketplaceSavedItem();
        $deleted = $savedModel->removeForUser($userId, $itemId);

        echo json_encode(['success' => true, 'saved' => false, 'deleted' => (bool)$deleted]);
        exit;
    }
}
