<?php

require_once(__DIR__ . "/../models/studyMaterial.php");
require_once(__DIR__ . "/../core/functions.php");


class StudyMaterial extends Controller
{
	private $allowedOrderBy = ['recent', 'popular', 'title'];
	private $allowedTypes = ['document', 'video', 'link'];
	private $mediaExpireTime = 300;

	private function getStudyMaterials($limit, $offset,$orderBy = 'recent', $searchTerm = '') {
		if (!in_array($orderBy, $this->allowedOrderBy)) {
			$orderBy = 'recent';
		}
	
		$studyMaterialModel = new StudyMaterialModel();
		$studyMaterialsData = $studyMaterialModel->getStudyMaterials($limit, $offset, $orderBy, $searchTerm);

		return $studyMaterialsData;
	}

	private function getMyStudyMaterials($limit, $offset) {

		$userId = $_SESSION['user_id'];
		$studyMaterialModel = new StudyMaterialModel();
		$studyMaterialsData = $studyMaterialModel->getMyStudyMaterials($limit, $offset, $userId);

		return $studyMaterialsData;
	}

	private function getCategories($limit, $offset, $searchTerm = '') {
		require_once(__DIR__ . "/../models/studyCategory.php");
		$studyCategoryModel = new StudyCategoryModel();
		$categories = $studyCategoryModel->getAllCategories($searchTerm, $limit, $offset);
		return $categories;
	}

	private function createVolunteerRecord($userId){
		require_once(__DIR__ . "/../models/studyMaterialVolunteer.php");
		$smVolunteerModel = new SMVolunteerModel();
		return $smVolunteerModel->createVolunteer($userId);
	}

	private function isVolunteer($userId){
		require_once(__DIR__ . "/../models/studyMaterialVolunteer.php");
		$smVolunteerModel = new SMVolunteerModel();
		return $smVolunteerModel->isVolunteer($userId);
	}

	private function getLinkAndType($id){
		$studyMaterialModel = new StudyMaterialModel();
		return $studyMaterialModel->getLinkAndType($id);
	}

	private function getSignedUrl($studyMaterialSK){
		return getCloudinarySignedURL($studyMaterialSK, $this->mediaExpireTime) ?? false;
	}

	private function incrementViewCount($id){
		$studyMaterialModel = new StudyMaterialModel();
		return $studyMaterialModel->incrementViewCount($id);
	}

	private function isOwnerOfSM($userId,$id){
		$studyMaterialModel = new StudyMaterialModel();
		$ownerId = $studyMaterialModel->getOwnerId($id);
		return $ownerId === $userId;
	}

	public function createNewSM(){
		if($_SERVER['REQUEST_METHOD'] === 'POST') {
			$data = parseRequestData();

			header('Content-Type: application/json');

			$title = $data['title'] ?? '';
			$description = $data['description'] ?? '';
			$category = $data['category'] ?? '';
			$link = $data['link'] ?? '';
			$type = $data['type'] ?? '';
			$files = $data['FILES'] ?? null;

			if(!in_array($type, $this->allowedTypes)){
				http_response_code(400);
				echo json_encode(['error' => 'Invalid type specified']);
				return;
			}

			if($type == 'link' && empty($link)){
					http_response_code(400);
					echo json_encode(['error' => 'Link is required for type "link"']);
					return;
			}

			if($type != 'link'){
				$link = uploadImageToCloudinary($files['file'], 'study_materials', true);
			}

			if($link === null){
				http_response_code(500);
				echo json_encode(['error' => 'File upload failed']);
				return;
			}

			$data = [
				'title' => $title,
				'description' => $description,
				'volunteer_id' => $_SESSION['user_id'],
				'url' => $link,
				'category_id' => $category,
				'type' => $type
			];
			
			// Would be prefered let the sql handle this through just an insert but our current design stops when sql error occurs
			if(!$this->isVolunteer($_SESSION['user_id'])) {
				$this->createVolunteerRecord($_SESSION['user_id']);
			}

			$studyMaterialModel = new StudyMaterialModel();
			$response = $studyMaterialModel->createSM($data);

			if(!$response){
				http_response_code(500);
				echo json_encode(['error' => 'Failed to create study material']);
				return;
			}

			echo json_encode([
				'success' => true,
			]);
		} else {
			http_response_code(405);
			echo json_encode(['error' => 'Method not allowed']);
		}
	}

	public function updateSM(){
		if($_SERVER['REQUEST_METHOD'] === 'POST') {
			$data = parseRequestData();

			header('Content-Type: application/json');

			$id = $data['id'] ?? null;
			$title = $data['title'] ?? '';
			$description = $data['description'] ?? '';
			$category = $data['category'] ?? '';

			if(!$id){
				http_response_code(400);
				echo json_encode(['error' => 'Study material ID is required']);
				return;
			}

			if(!$this->isOwnerOfSM($_SESSION['user_id'], $id)){
				http_response_code(403);
				echo json_encode(['error' => 'You do not have permission to edit this study material']);
				return;
			}

			$updateData = [
				'title' => $title,
				'description' => $description,
				'category_id' => $category,
				'is_updated' => true
			];

			$studyMaterialModel = new StudyMaterialModel();
			$response = $studyMaterialModel->updateSM($id, $updateData);

			if(!$response){
				http_response_code(500);
				echo json_encode(['error' => 'Failed to update study material']);
				return;
			}

			echo json_encode([
				'success' => true,
			]);
		} else {
			http_response_code(405);
			echo json_encode(['error' => 'Method not allowed']);
		}
	}

	public function deleteSM(){
		if($_SERVER['REQUEST_METHOD'] === 'POST') {
			$data = parseRequestData();

			header('Content-Type: application/json');

			$id = $data['id'] ?? null;

			if(!$id){
				http_response_code(400);
				echo json_encode(['error' => 'Study material ID is required']);
				return;
			}

			if(!$this->isOwnerOfSM($_SESSION['user_id'], $id)){
				http_response_code(403);
				echo json_encode(['error' => 'You do not have permission to delete this study material']);
				return;
			}

			$studyMaterialModel = new StudyMaterialModel();
			$response = $studyMaterialModel->deleteSM($id);

			if(!$response){
				http_response_code(500);
				echo json_encode(['error' => 'Failed to delete study material']);
				return;
			}

			echo json_encode([
				'success' => true,
			]);
		} else {
			http_response_code(405);
			echo json_encode(['error' => 'Method not allowed']);
		}
	}

	public function show($id){

		$smData = $this->getLinkAndType($id);
		if(!$smData){
			http_response_code(404);
			echo json_encode(['error' => 'Study material not found']);
			return;
		}

		$type = $smData['type'];
		$url = $smData['url'];
		$finalUrl = '';

		switch($type){
			case 'link':
				$finalUrl = $url;
				break;

			case 'document':
			case 'video':
				$finalUrl = $this->getSignedUrl($url);
				if($finalUrl){break;}

			default:
				http_response_code(404);
				echo json_encode(['error' => 'Study material not found']);
		}

		$this->incrementViewCount($id);
		header('Location: ' . $finalUrl);
		exit;
	}



	public function scrollable(){
		
		if($_SERVER['REQUEST_METHOD'] === 'POST') {
			$data = parseRequestData();
			error_log("Received data: " . print_r($data, true));

			header('Content-Type: application/json');

			switch($data['scrollIdentifier']) {
				case 'getStudyMaterials':
					$searchTerm = $data['context']['searchTerm'] ?? '';
					$orderBy = $data['context']['orderBy'] ?? '';
					$studyMaterials = $this->getStudyMaterials($data['limit'], $data['offset'], $orderBy, $searchTerm);
					echo json_encode($studyMaterials);
					break;

				case 'getMyStudyMaterials':
					$studyMaterials = $this->getMyStudyMaterials($data['limit'], $data['offset']);
					echo json_encode($studyMaterials);
					break;

				case 'getCategories':
					$searchTerm = $data['context']['query'] ?? '';
					$categories = $this->getCategories($data['limit'], $data['offset'], $searchTerm);
					echo json_encode($categories);
					break;

				default:
					http_response_code(400);
					echo json_encode(['error' => 'Invalid scroll identifier']);
			}
		}
	}

	public function index()
	{
		$this->view('studymaterial', [
			'title' => 'Study Materials • UniConnect',
			'head' => '
			<link rel="stylesheet" href="/assets/css/pages/home.css">
			<link rel="stylesheet" href="/assets/css/components/feed.css">
			<link rel="stylesheet" href="/assets/css/components/studyMaterialControl.css">
			<link rel="stylesheet" href="/assets/css/components/studyMaterialCard.css">
			<link rel="stylesheet" href="/assets/css/components/manageStudyMaterial.css">
			<link rel="stylesheet" href="/assets/css/components/modal.css">
			<link rel="stylesheet" href="/assets/css/components/navbar.css">
			<link rel="stylesheet" href="/assets/css/components/navPanel.css">
			<link rel="stylesheet" href="/assets/css/components/widgetPanel.css">
			<link rel="stylesheet" href="/assets/css/pages/studyMaterial.css">
			',
		]);
	}
}
