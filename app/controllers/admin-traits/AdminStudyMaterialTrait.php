<?php

trait AdminStudyMaterialTrait
{
    public function Stu()
    {
        if (empty($_SESSION['is_admin'])) {
            header("Location: /admin");
            exit;
        }
    }
}
