<?php
function startSession($path = null)
{
    if ($path === null) {
        $path = __DIR__ . "/../storage/sessions";
    }

    if (!is_dir($path)) {
        mkdir($path, 0777, true);
    }

    session_save_path($path);
    session_start();
}