<?php

require_once(__DIR__ . "/../models/studyMaterial.php");
require_once(__DIR__ . "/../core/functions.php");


class StudyMaterial extends Controller
{

	private function getStudyMaterials($limit, $offset,$orderBy = 'recent', $searchTerm = '') {
		$allowedOrderBy = ['recent', 'popular', 'title'];
		if (!in_array($orderBy, $allowedOrderBy)) {
			$orderBy = 'recent';
		}
	
		$studyMaterialModel = new StudyMaterialModel();
		$studyMaterialsData = $studyMaterialModel->getStudyMaterials($limit, $offset, $orderBy, $searchTerm);

		return $studyMaterialsData;
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
