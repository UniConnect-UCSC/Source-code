<?php

class Controller
{
    public function view($view, $data = [])
    {
        if (!empty($data)) {
            extract($data);
        }

        $filename = __DIR__ . "/../views/" . $view . ".view.php";
        if (file_exists($filename)) {
            ob_start(); // Start buffering BEFORE requiring the view
            require $filename;
            $content = ob_get_clean();
            require(__DIR__ . "/../views/layouts/main.php");
        } else {
            require(__DIR__ . "/../views/404.view.php");
        }
    }
}
