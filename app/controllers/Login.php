<?php

require_once(__DIR__ . "/../models/User.php");

class Login extends Controller
{
    public function index()
    {

        $data = ['errors' => []];

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $email    = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $data['errors']['email'] = 'Please enter a valid email address';
            }

            if ($password === '') {
                $data['errors']['password'] = 'Password is required';
            }

            if (empty($data['errors'])) {
                $userModel = new User();
                $user = $userModel->first(['email' => $email]);

                if ($user && password_verify($password, $user->password)) {
                    // Regenerate to prevent fixation, set session, then explicitly write+redirect
                    session_regenerate_id(true);
                    $_SESSION['user_id']    = $user->id;
                    $_SESSION['user_email'] = $user->email;
                    $_SESSION['user_fName'] = $user->f_name;
                    $_SESSION['user_lName'] = $user->l_name;

                    // Ensure session is saved before redirect
                    session_write_close();
                    header('Location: /');
                    exit;
                } else {
                    $data['errors']['email'] = 'Wrong email or password';
                }
            }
        }

        $this->view('login', [
            'title'  => 'Log In | UniConnect',
            'head'   => '<link rel="stylesheet" href="/assets/css/pages/login.css">',
            'errors' => $data['errors'],
        ]);
    }
}
