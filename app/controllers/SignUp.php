<?php

require_once(__DIR__ . "/../models/User.php");
require_once(__DIR__ . "/../models/University.php");

class SignUp extends Controller
{
    public function index()
    {
        $data = ['errors' => []];

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $bday = $_POST['birthday'] ?? '';
            $fName = $_POST['fName'] ?? '';
            $lName = $_POST['lName'] ?? '';

            //Already validated with Javascript, but added for safe measures
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $data['errors']['email'] = 'Please enter a valid email address';
            }

            if ($password === '') {
                $data['errors']['password'] = 'Password is required';
            }

            //Check if the email is a valid uni email
            //Code goes here

            //Match email to the university
            $universityId = "";
            if ($email) {
                $domain = substr(strrchr($email, "@"), 1);

                $domain = explode(".", $domain);
                if (count($domain) == 2) {
                    $domain = implode(".", array_slice($domain, -2));
                } else {
                    $domain = implode(".", array_slice($domain, -3));
                }

                $universityModel = new University();

                $university = $universityModel->first([
                    "email_domain" => $domain,
                ]);
                $universityId = $university ? $university->id : "";
            }

            //Check if email already exists
            $userModel = new User();
            $user = $userModel->first(["email" => $email]);

            if ($user) {
                $data['errors']['email'] = 'This email already exists';
            }


            $userData = [
                'f_name' => $fName,
                'l_name' => $lName,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'birthday' => $bday,
                'university_id' => $universityId,
            ];

            if (empty($data['errors'])) {
                $user = $userModel->insertAndFetch($userData);

                if ($user) {
                    session_regenerate_id(true);
                    $_SESSION['user_id']    = $user->id;
                    $_SESSION['user_email'] = $user->email;
                    $_SESSION['user_fName'] = $user->f_name;
                    $_SESSION['user_lName'] = $user->l_name;
                    $_SESSION['user_universityID'] = $user->university;

                    // Ensure session is saved before redirect
                    session_write_close();
                    header('Location: /');
                    exit;
                }
            }
        }

        $this->view(
            'signup',
            [
                'title' => 'Sign Up | UniConnect',
                'head' => '<link rel="stylesheet" href="/assets/css/pages/signup.css">',
                'errors' => $data['errors'],
            ],
        );
    }
}
