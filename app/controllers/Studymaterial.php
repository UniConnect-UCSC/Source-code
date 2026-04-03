<?php
class StudyMaterial extends Controller
{
	public function index()
	{
		$this->view('studymaterial', [
			'title' => 'Study Materials • UniConnect',
			'head' => '
			<link rel="stylesheet" href="/assets/css/pages/home.css">
			<link rel="stylesheet" href="/assets/css/components/feed.css">
			<link rel="stylesheet" href="/assets/css/components/eventControls.css">
			<link rel="stylesheet" href="/assets/css/components/navbar.css">
			<link rel="stylesheet" href="/assets/css/components/navPanel.css">
			<link rel="stylesheet" href="/assets/css/components/widgetPanel.css">
			<link rel="stylesheet" href="/assets/css/pages/studyMaterial.css">
			',
		]);
	}

	public function manage()
	{
		$this->view('manage_studymaterials', [
			'title' => 'Manage Study Materials • UniConnect',
			'head' => '
			<link rel="stylesheet" href="/assets/css/pages/home.css">
			<link rel="stylesheet" href="/assets/css/components/feed.css">
			<link rel="stylesheet" href="/assets/css/components/eventControls.css">
			<link rel="stylesheet" href="/assets/css/components/navbar.css">
			<link rel="stylesheet" href="/assets/css/components/navPanel.css">
			<link rel="stylesheet" href="/assets/css/components/widgetPanel.css">
			<link rel="stylesheet" href="/assets/css/pages/studyMaterial.css">
			',
		]);
	}
}
