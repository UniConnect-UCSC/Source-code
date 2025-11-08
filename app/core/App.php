<?php
class App
{
    private $controller = 'Home';
    private $method = 'index';

    private function splitURL()
    {
        $URL = $_GET['url'] ?? "home";
        $URL = trim($URL, "/");
        $URL = filter_var($URL, FILTER_SANITIZE_URL);
        if ($URL === '') return ["home"];
        return explode("/", $URL);
    }

    public function loadController()
    {
        $URL = $this->splitURL();
        $filename = __DIR__ . "/../controllers/" . ucfirst($URL[0]) . ".php";

        /** Select Controller **/
        if (file_exists($filename)) {
            require $filename;
            $this->controller = ucfirst($URL[0]);
            unset($URL[0]);
        } else {
            require __DIR__ . "/../controllers/_404.php";
            $this->controller = "_404";
        }

        $controller = new $this->controller;

        // reindex remaining segments
        $URL = array_values($URL);

        /** Select Method **/
        if (!empty($URL[0])) {
            // if method exists on controller, use it
            if (method_exists($controller, $URL[0])) {
                $this->method = $URL[0];
                unset($URL[0]);
                $URL = array_values($URL);
            } else {
                // treat UUIDs or slug-like identifiers as 'show' if controller has show()
                $isUuid = preg_match(
                    '/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-5][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}$/',
                    $URL[0]
                );
                $isSlug = preg_match('/^[a-zA-Z0-9-_]+$/', $URL[0]);

                if (method_exists($controller, 'show') && ($isUuid || $isSlug)) {
                    $this->method = 'show';
                    // leave $URL as params (first item is identifier)
                } else {
                    // fallback to 404 controller
                    require __DIR__ . "/../controllers/_404.php";
                    $controller = new _404;
                    $this->method = 'index';
                    $URL = [];
                }
            }
        }

        $params = $URL ? array_values($URL) : [];

        call_user_func_array([$controller, $this->method], $params);
    }
}