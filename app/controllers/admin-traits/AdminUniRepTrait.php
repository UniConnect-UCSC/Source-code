<?php

trait AdminUniRepTrait
{
    public function unirep()
    {
        if (empty($_SESSION['is_admin'])) {
            header("Location: /admin");
            exit;
        }

        $universityModel = new University();

        $universities = $universityModel->where(
            [],
            15,
            0,
            [],
            [],
            ['id', 'name']
        ) ?: [];

        $this->view('adminUniRep', [
            'title' => 'Admin Dashboard | UniConnect',
            'head' => '
            <link rel="stylesheet" href="/assets/css/pages/admin.css">
            <link rel="stylesheet" href="/assets/css/pages/adminPosts.css">
            <link rel="stylesheet" href="/assets/css/components/navbar.css">
            <link rel="stylesheet" href="/assets/css/components/navPanel.css">
            <link rel="stylesheet" href="/assets/css/components/uniRepTable.css">
            
            ',
            'universities' => $universities,
        ]);
    }
}
