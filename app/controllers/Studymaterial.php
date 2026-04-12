<?php

require_once(__DIR__ . "/../models/studyMaterial.php");


class StudyMaterial extends Controller
{
	public function index()
	{
		$this->view('studymaterial', [
			'title' => 'Study Materials • UniConnect',
			'head' => '
			<link rel="stylesheet" href="/assets/css/pages/home.css">
			<link rel="stylesheet" href="/assets/css/components/feed.css">
			<link rel="stylesheet" href="/assets/css/components/studyMaterialControl.css">
			<link rel="stylesheet" href="/assets/css/components/studyMaterialCard.css">
			<link rel="stylesheet" href="/assets/css/components/modal.css">
			<link rel="stylesheet" href="/assets/css/components/navbar.css">
			<link rel="stylesheet" href="/assets/css/components/navPanel.css">
			<link rel="stylesheet" href="/assets/css/components/widgetPanel.css">
			<link rel="stylesheet" href="/assets/css/pages/studyMaterial.css">
			',
		]);
	}
}
