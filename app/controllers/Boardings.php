<?php
require_once(__DIR__ . "/../models/BoardingRoom.php");
require_once(__DIR__ . "/../models/Location.php");
require_once(__DIR__ . "/../models/BoardingRoomImage.php");
require_once(__DIR__ . "/../models/BoardingLocations.php");

require_once __DIR__ . '/../core/functions.php';

class Boardings extends Controller
{
    private $boardingRoomImageColumns = null;

    private function collectUploadedImages($fieldName = 'images')
    {
        $files = [];

        if (!isset($_FILES[$fieldName])) {
            return $files;
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
            return $files;
        }

        if (($input['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            $files[] = $input;
        }

        return $files;
    }

    private function uploadBoardingImages($uploadedFiles)
    {
        $uploadedImageUrls = [];

        foreach ($uploadedFiles as $file) {
            $imageURL = uploadImageToCloudinary($file, 'uniconnect_boardings');
            if (!$imageURL) {
                throw new Exception('Failed to upload one or more images');
            }
            $uploadedImageUrls[] = $imageURL;
        }

        return $uploadedImageUrls;
    }

    private function getBoardingRoomImageColumnMap($imageModel)
    {
        if ($this->boardingRoomImageColumns !== null) {
            return $this->boardingRoomImageColumns;
        }

        $columns = $imageModel->query("SELECT column_name FROM information_schema.columns WHERE table_name = 'boarding_room_images'");
        if (!$columns) {
            throw new Exception('boarding_room_images table is missing or inaccessible');
        }

        $columnNames = array_map(function ($row) {
            return $row->column_name;
        }, $columns);

        $roomColumn = null;
        foreach (['room_id', 'boarding_room_id'] as $candidate) {
            if (in_array($candidate, $columnNames, true)) {
                $roomColumn = $candidate;
                break;
            }
        }

        $imageColumn = null;
        foreach (['img_url', 'image_url', 'image', 'url', 'image_path'] as $candidate) {
            if (in_array($candidate, $columnNames, true)) {
                $imageColumn = $candidate;
                break;
            }
        }

        if (!$roomColumn || !$imageColumn) {
            throw new Exception('boarding_room_images schema mismatch: expected room and image columns');
        }

        $this->boardingRoomImageColumns = [
            'room' => $roomColumn,
            'image' => $imageColumn,
        ];

        return $this->boardingRoomImageColumns;
    }

    public function index()
    {
        $this->view('boardings', [
            'title' => 'UniConnect | Boardings',
            'head' => '
                <link rel="stylesheet" href="/assets/css/components/navbar.css">
                <link rel="stylesheet" href="/assets/css/components/navPanel.css">
               
                <link rel="stylesheet" href="/assets/css/pages/boardings.css">
            
                <link rel="stylesheet" href="/assets/css/pages/home.css">
                <link rel="stylesheet" href="/assets/css/components/createPost.css">
                <link rel="stylesheet" href="/assets/css/components/boardingsFeed.css">
                <link rel="stylesheet" href="/assets/css/components/boardingsCard.css">
                <script src="/assets/js/boardings.js" defer></script>
                <script src="/assets/js/boardingCard.js" defer></script>
       
                '
        ]);
    }

    public function searchListings()
    {
        header('Content-Type: application/json');

        try {
            $roomModel = new BoardingRoom();
            $locationModel = new Location();

            // Get search parameters
            $searchQuery = $_GET['search'] ?? '';
            $city = $_GET['city'] ?? '';
            $district = $_GET['district'] ?? '';
            $gender = $_GET['gender'] ?? '';
            $category = $_GET['category'] ?? '';
            $facilities = $_GET['facilities'] ?? '';
            $minRent = $_GET['min_rent'] ?? '';
            $maxRent = $_GET['max_rent'] ?? '';
            $status = $_GET['status'] ?? '';

            // Build the SQL query
            $conditions = [];
            $params = [];
            $conditions[] = "COALESCE(LOWER(br.status::text), 'available') <> 'closed'";
            
            

            // Search filter (Main search bar)
            if (!empty($searchQuery)) {
                $conditions[] = "(l.city ILIKE :search OR l.district ILIKE :search)";
                $params['search'] = '%' . $searchQuery . '%';
            }

            // Status filter (default to available)
            if (!empty($status) && strtolower($status) !== 'closed') {
                $conditions[] = "LOWER(br.status::text) = LOWER(:status)";
                $params['status'] = $status;
            }

            // Gender filter
            if (!empty($gender)) {
                $conditions[] = "LOWER(br.gender) = LOWER(:gender)";
                $params['gender'] = trim($gender);
            }

            // Category filter
            if (!empty($category)) {
                $conditions[] = "LOWER(br.category) = LOWER(:category)";
                $params['category'] = trim($category);
            }

            // Facilities filter
            if (!empty($facilities)) {
                $conditions[] = "LOWER(br.facilities) LIKE LOWER(:facilities)";
                $params['facilities'] = '%' . trim($facilities) . '%';
            }

            // Price range filter
            if (!empty($minRent)) {
                $conditions[] = "br.rent >= :min_rent";
                $params['min_rent'] = floatval($minRent);
            }
            if (!empty($maxRent)) {
                $conditions[] = "br.rent <= :max_rent";
                $params['max_rent'] = floatval($maxRent);
            }

            // Location filters
            if (!empty($city)) {
                $conditions[] = "l.city ILIKE :city";
                $params['city'] = '%' . $city . '%';
            }
            if (!empty($district)) {
                $conditions[] = "l.district ILIKE :district";
                $params['district'] = '%' . $district . '%';
            }

            // Build WHERE clause
            $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

            // Execute query with JOIN and aggregate room images
            $query = "SELECT br.*, l.city, l.district, bri.image_urls_csv
                      FROM boarding_rooms br 
                      LEFT JOIN locations l ON br.location_id = l.id
                      LEFT JOIN (
                          SELECT room_id, STRING_AGG(img_url, '|||' ORDER BY id) AS image_urls_csv
                          FROM boarding_room_images
                          GROUP BY room_id
                      ) bri ON bri.room_id = br.id
                      {$whereClause}
                      ORDER BY br.created_at DESC";

            $results = $roomModel->query($query, $params);

            echo json_encode([
                'success' => true,
                'rooms' => $results ?? [],
                'count' => count($results ?? [])
            ]);
        } catch (Exception $e) {
            error_log("Boardings::searchListings error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }




    public function myListings()
    {
        $this->view('myListings', [
            'title' => 'My Listings | UniConnect',
            'head' => '
            <link rel="stylesheet" href="/assets/css/pages/boardings.css">
            <link rel="stylesheet" href="/assets/css/pages/home.css">
            <link rel="stylesheet" href="/assets/css/components/feed.css">
            <link rel="stylesheet" href="/assets/css/components/navbar.css">
            <link rel="stylesheet" href="/assets/css/components/navPanel.css">
            <link rel="stylesheet" href="/assets/css/components/boardingsCard.css">
            <link rel="stylesheet" href="/assets/css/components/myListings.css">
            <script src="/assets/js/boardingCard.js" defer></script>

            ',
        ]);
    }

    public function createListing()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');

            try {
                $imageModel = new BoardingRoomImage();
                $roomModel = new BoardingRoom();
                $locationModel = new Location();

                // Support both images[] (new) and image (legacy)
                $uploadedImages = $this->collectUploadedImages('images');
                if (empty($uploadedImages)) {
                    $uploadedImages = $this->collectUploadedImages('image');
                }
                if (empty($uploadedImages)) {
                    throw new Exception('At least one image is required');
                }
                if (count($uploadedImages) > 5) {
                    throw new Exception('You can upload maximum 5 images');
                }

                $uploadedImageUrls = $this->uploadBoardingImages($uploadedImages);
                $imageURL = $uploadedImageUrls[0] ?? null;
                

                // Handle location - either get existing or create new
                $locationId = null;
                if (!empty($_POST['city']) && !empty($_POST['district'])) {
                    $existingLocation = $locationModel->first([
                        'city' => trim($_POST['city']),
                        'district' => trim($_POST['district'])
                    ]);

                    if ($existingLocation) {
                        $locationId = $existingLocation->id;
                    } else {
                        $locationData = [
                            'city' => trim($_POST['city']),
                            'district' => trim($_POST['district'])
                        ];
                        $newLocation = $locationModel->insertAndFetch($locationData);
                        $locationId = $newLocation->id;
                    }
                }

                $description = trim($_POST['description'] ?? '');

                // Validate status
                $validStatuses = ['available', 'occupied', 'closed'];
                $status = strtolower($_POST['status'] ?? 'available');
                if (!in_array($status, $validStatuses)) {
                    $status = 'available';
                }

                // Validate category
                $validCategories = ['single room', 'double room', 'shared room', 'apartment', 'hostel'];
                $category = strtolower($_POST['category'] ?? 'single room');
                if (!in_array($category, $validCategories)) {
                    $category = 'single room';
                }

                // Validate facilities
                $validFacilities = ['wifi', 'meals available', 'kitchen access', 'common bathroom', 'attached bathroom', 'ac', 'fully furnished'];
                $selectedFacilities = $_POST['facilities'] ?? [];
                if (!is_array($selectedFacilities)) {
                    $selectedFacilities = [$selectedFacilities];
                }
                $selectedFacilities = array_values(array_unique(array_map(function ($facility) {
                    return strtolower(trim((string)$facility));
                }, $selectedFacilities)));
                $selectedFacilities = array_values(array_intersect($selectedFacilities, $validFacilities));
                if (empty($selectedFacilities)) {
                    $selectedFacilities = ['wifi'];
                }
                $facilities = implode(', ', $selectedFacilities);

                $validgender = ['male students', 'female students', 'male workers', 'female workers', 'any'];
                $gender = strtolower($_POST['gender'] ?? 'male students');
                if (!in_array($gender, $validgender)) {
                    $gender = 'male students';
                }
                $contactNumber = trim($_POST['contact_number'] ?? '');
                if ($contactNumber === '') {
                    throw new Exception('Contact number is required');
                }
                if (!preg_match('/^[0-9+\-\s]{7,20}$/', $contactNumber)) {
                    throw new Exception('Please enter a valid contact number');
                }



                // Create boarding room data array
                $roomData = [
                    'student_id' => $_SESSION['user_id'],
                    'location_id' => $locationId,
                    'rent' => floatval($_POST['rent']),
                    'occupancy' => intval($_POST['occupancy']),
                    'gender' => $gender,
                    'status' => $status,
                    'category' => $_POST['category'] ?? 'Single Room',
                    'facilities' => $facilities,
                    'image_url' => $imageURL,
                    'contact_number' => $contactNumber,
                    'description' => $description

                ];

                error_log("Creating boarding room with data: " . print_r($roomData, true));

                // Insert room and get the created room
                $roomId = $roomModel->insertAndFetch($roomData);

                if (!$roomId) {
                    throw new Exception('Failed to create listing in database');
                }

                $columnMap = $this->getBoardingRoomImageColumnMap($imageModel);

                foreach ($uploadedImageUrls as $uploadedImageUrl) {
                    $imageModel->insert(
                    [$columnMap['room'], $columnMap['image']], 
                    [$roomId->id, $uploadedImageUrl]
                    );
                }

                echo json_encode([
                    'success' => true,
                    'room_id' => $roomId->id,
                    'image_url' => $imageURL,
                    'images' => $uploadedImageUrls,
                    'message' => 'Listing created successfully'
                ]);
            } catch (Exception $e) {
                error_log("Boardings::createListing error: " . $e->getMessage());
                error_log("Stack trace: " . $e->getTraceAsString());
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            exit;
        } else {
            header('Location: /boardings/postListing');
            exit;
        }
    }



    public function editListing($roomId = null)
    {
        // Handle POST - Save the edited listing
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');

            try {
                $roomId = $_POST['room_id'] ?? null;
                if (!$roomId) {
                    throw new Exception('No listing ID provided');
                }
                $imageModel = new BoardingRoomImage();
                $roomModel = new BoardingRoom();
                $locationModel = new Location();

                // Verify ownership
                $existingRoom = $roomModel->first(['id' => $roomId]);
                if (!$existingRoom || $existingRoom->student_id !== $_SESSION['user_id']) {
                    http_response_code(403);
                    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                    exit;
                }

                // Handle location update
                $locationId = $existingRoom->location_id;
                if (!empty($_POST['city']) && !empty($_POST['district'])) {
                    $existingLocation = $locationModel->first([
                        'city' => trim($_POST['city']),
                        'district' => trim($_POST['district'])
                    ]);

                    if ($existingLocation) {
                        $locationId = $existingLocation->id;
                    } else {
                        $locationData = [
                            'city' => trim($_POST['city']),
                            'district' => trim($_POST['district'])
                        ];
                        $newLocation = $locationModel->insertAndFetch($locationData);
                        $locationId = $newLocation->id;
                    }
                }



                // Validate status
                $validStatuses = ['available', 'occupied', 'closed'];
                $status = strtolower($_POST['status'] ?? $existingRoom->status);
                if (!in_array($status, $validStatuses)) {
                    $status = $existingRoom->status;
                }



                // Validate category
                $validCategories = ['single room', 'double room', 'shared room', 'apartment', 'hostel'];
                $category = strtolower($_POST['category'] ?? $existingRoom->category);
                if (!in_array($category, $validCategories)) {
                    $category = $existingRoom->category;
                }

                // Validate facilities
                $validFacilities = ['wifi', 'meals available', 'kitchen access', 'common bathroom', 'attached bathroom', 'ac', 'fully furnished'];
                $existingFacilities = array_values(array_filter(array_map(function ($facility) {
                    return strtolower(trim((string)$facility));
                }, explode(',', (string)($existingRoom->facilities ?? '')))));
                $selectedFacilities = $_POST['facilities'] ?? $existingFacilities;
                if (!is_array($selectedFacilities)) {
                    $selectedFacilities = [$selectedFacilities];
                }
                $selectedFacilities = array_values(array_unique(array_map(function ($facility) {
                    return strtolower(trim((string)$facility));
                }, $selectedFacilities)));
                $selectedFacilities = array_values(array_intersect($selectedFacilities, $validFacilities));
                if (empty($selectedFacilities)) {
                    $selectedFacilities = !empty($existingFacilities) ? $existingFacilities : ['wifi'];
                }
                $facilities = implode(', ', $selectedFacilities);

                $validGender = ['male students', 'female students', 'male workers', 'female workers', 'any'];
                $gender = strtolower($_POST['gender'] ?? $existingRoom->gender);
                if (!in_array($gender, $validGender)) {
                    $gender = $existingRoom->gender;
                }
                $contactNumber = trim($_POST['contact_number'] ?? '');
                if ($contactNumber === '') {
                    throw new Exception('Contact number is required');
                }
                if (!preg_match('/^[0-9+\-\s]{7,20}$/', $contactNumber)) {
                    throw new Exception('Please enter a valid contact number');
                }
                $roomData['contact_number'] = $contactNumber;
                $description = trim($_POST['description'] ?? '');

                $roomData = [
                    'rent' => floatval($_POST['rent']),
                    'occupancy' => intval($_POST['occupancy']),
                    'gender' => $gender,
                    'status' => $status,
                    'category' => $_POST['category'] ?? $existingRoom->category,
                    'facilities' => $facilities,
                    'location_id' => $locationId,
                     'description' => ($description === '' ? null : $description),
                    'contact_number' => $contactNumber,
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $columnMap = $this->getBoardingRoomImageColumnMap($imageModel);

                $removeImageIds = $_POST['remove_image_ids'] ?? [];
                if (!is_array($removeImageIds)) {
                    $removeImageIds = [$removeImageIds];
                }
                $removeImageIds = array_values(array_unique(array_filter(array_map(function ($value) {
                    return intval($value);
                }, $removeImageIds), function ($value) {
                    return $value > 0;
                })));

                // Handle image upload if provided (supports images[] and legacy image)
                $uploadedImages = $this->collectUploadedImages('images');
                if (empty($uploadedImages)) {
                    $uploadedImages = $this->collectUploadedImages('image');
                }

                if (count($uploadedImages) > 5) {
                    throw new Exception('You can upload maximum 5 images');
                }

                $currentRoomImages = $imageModel->where([[$columnMap['room'], '=', $roomId]], null, null, ['id' => 'ASC']) ?: [];
                $currentImageIds = [];
                foreach ($currentRoomImages as $currentRoomImage) {
                    if (isset($currentRoomImage->id)) {
                        $currentImageIds[(int)$currentRoomImage->id] = true;
                    }
                }

                $validRemoveCount = 0;
                foreach ($removeImageIds as $removeImageId) {
                    if (isset($currentImageIds[(int)$removeImageId])) {
                        $validRemoveCount++;
                    }
                }

                $finalImageCount = count($currentRoomImages) - $validRemoveCount + count($uploadedImages);
                if ($finalImageCount < 1) {
                    throw new Exception('At least one image is required');
                }
                if ($finalImageCount > 5) {
                    throw new Exception('You can keep maximum 5 images');
                }

                $uploadedImageUrls = !empty($uploadedImages) ? $this->uploadBoardingImages($uploadedImages) : [];

                $result = $roomModel->update($roomId, $roomData);

                if ($result) {
                    $imagesChanged = false;

                    foreach ($removeImageIds as $imageId) {
                        $imageRow = $imageModel->first(['id' => $imageId]);
                        if ($imageRow && (($imageRow->{$columnMap['room']} ?? null) === $roomId)) {
                            $imageModel->delete([['id', '=', $imageId]]);
                            $imagesChanged = true;
                        }
                    }

                    if (!empty($uploadedImageUrls)) {
                    foreach ($uploadedImageUrls as $uploadedImageUrl) {
                        $imageModel->insert(
                            [$columnMap['room'], $columnMap['image']],
                            [$roomId, $uploadedImageUrl]
                        );
                    }
                        $imagesChanged = true;
                    }

                    if ($imagesChanged) {
                        $roomImages = $imageModel->where([[$columnMap['room'], '=', $roomId]], null, null, ['id' => 'ASC']);
                        $primaryImageUrl = null;

                        if (!empty($roomImages)) {
                            foreach ($roomImages as $roomImage) {
                                $imageValue = $roomImage->{$columnMap['image']} ?? null;
                                if (!empty($imageValue)) {
                                    $primaryImageUrl = $imageValue;
                                    break;
                                }
                            }
                        }

                        $roomModel->update($roomId, [
                            'image_url' => $primaryImageUrl,
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                }

                if ($result) {
                    // Redirect to myListings on success
                    header('Location: /boardings/myListings?success=updated');
                    exit;
                } else {
                    throw new Exception('Failed to update listing');
                }
            } catch (Exception $e) {
                error_log("Boardings::editListing error: " . $e->getMessage());
                header('Location: /boardings/myListings?error=' . urlencode($e->getMessage()));
                exit;
            }
        }

        // Handle GET - Display the edit form
        if (!$roomId) {
            header('Location: /boardings/myListings');
            exit;
        }
        $imageModel = new BoardingRoomImage();
        $roomModel = new BoardingRoom();
        $locationModel = new Location();

        // Get the room
        $room = $roomModel->first(['id' => $roomId]);


        // Verify ownership
        if (!$room || $room->student_id !== $_SESSION['user_id']) {
            header('Location: /boardings/myListings');
            exit;
        }

        // Get location details
        $location = null;
        if ($room->location_id) {
            $location = $locationModel->first(['id' => $room->location_id]);
        }

        $currentImages = [];
        try {
            $columnMap = $this->getBoardingRoomImageColumnMap($imageModel);
            $roomImages = $imageModel->where([[$columnMap['room'], '=', $room->id]], null, null, ['id' => 'ASC']);

            if (!empty($roomImages)) {
                foreach ($roomImages as $roomImage) {
                    $imageValue = $roomImage->{$columnMap['image']} ?? null;
                    if (!empty($imageValue)) {
                        $currentImages[] = [
                            'id' => $roomImage->id ?? null,
                            'url' => $imageValue
                        ];
                    }
                }
            }
        } catch (Exception $e) {
            error_log('Boardings::editListing image fetch warning: ' . $e->getMessage());
        }

        if (empty($currentImages) && !empty($room->image_url)) {
            $currentImages[] = [
                'id' => null,
                'url' => $room->image_url
            ];
        }

        // Get status options
        $statusOptions = $roomModel->getStatusOptions();

        $this->view('editListing', [
            'title' => 'Edit Listing | UniConnect',
            'room' => $room,
            'location' => $location,
            'currentImages' => $currentImages,

            'statusOptions' => $statusOptions,
            'head' => '
              <link rel="stylesheet" href="/assets/css/pages/boardings.css">
            <link rel="stylesheet" href="/assets/css/pages/home.css">
            <link rel="stylesheet" href="/assets/css/components/feed.css">
            <link rel="stylesheet" href="/assets/css/components/navbar.css">
            <link rel="stylesheet" href="/assets/css/components/navPanel.css">
            <link rel="stylesheet" href="/assets/css/components/boardingCard.css">
            <link rel="stylesheet" href="/assets/css/components/editListing.css">
            <script src="/assets/js/boardingCard.js" defer></script>
            <script src="/assets/js/boardings.js" defer></script>

        '
        ]);
    }

    public function details($roomId = null)
    {
        if (!$roomId) {
            header('Location: /boardings');
            exit;
        }

        $roomModel = new BoardingRoom();
        $imageModel = new BoardingRoomImage();
        $locationModel = new Location();

        $room = $roomModel->first(['id' => $roomId]);
        if (!$room) {
            header('Location: /boardings');
            exit;
        }

        $location = null;
        if (!empty($room->location_id)) {
            $location = $locationModel->first(['id' => $room->location_id]);
        }

        $imageUrls = [];
        if (!empty($room->id)) {
            $roomImages = $imageModel->where([['room_id', '=', $room->id]], null, null, ['id' => 'ASC']);
            if (!empty($roomImages)) {
                foreach ($roomImages as $roomImage) {
                    if (!empty($roomImage->img_url)) {
                        $imageUrls[] = $roomImage->img_url;
                    }
                }
            }
        }

        if (empty($imageUrls) && !empty($room->image_url)) {
            $imageUrls[] = $room->image_url;
        }

        $this->view('boardingDetails', [
            'title' => 'Boarding Details | UniConnect',
            'room' => $room,
            'location' => $location,
            'imageUrls' => $imageUrls,
            'head' => '
                <link rel="stylesheet" href="/assets/css/pages/boardings.css">
                <link rel="stylesheet" href="/assets/css/pages/boardingDetails.css">
                <link rel="stylesheet" href="/assets/css/pages/home.css">
                <link rel="stylesheet" href="/assets/css/components/navbar.css">
                <link rel="stylesheet" href="/assets/css/components/navPanel.css">
                <link rel="stylesheet" href="/assets/css/components/boardingsCard.css">
            '
        ]);
    }

    public function deleteListing()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $roomId = $_POST['delete_room_id'] ?? null;
        if (!$roomId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'No listing ID provided']);
            exit;
        }

        try {
            $roomModel = new BoardingRoom();

            $room = $roomModel->first(['id' => $roomId]);
            if (!$room) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Listing not found']);
                exit;
            }

            // Verify ownership
            if ($room->student_id !== $_SESSION['user_id']) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit;
            }

            $result = $roomModel->delete([['id', '=', $roomId]]);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Listing deleted successfully']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to delete listing']);
            }
        } catch (Exception $e) {
            error_log("Boardings::deleteListing error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
}
