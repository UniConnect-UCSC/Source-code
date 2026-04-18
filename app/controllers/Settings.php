<?php
require_once __DIR__ . '/../models/User.php';

class Settings extends Controller
{
    public function index()
    {
        ob_start();

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            redirect('/login');
        }

        $userModel = new User();
        $flash = ['type' => null, 'message' => null];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');

            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

            if (str_contains($contentType, 'application/json')) {
                $body = json_decode(file_get_contents('php://input'), true) ?? [];
                $action = $body['action'] ?? '';
                $_POST = array_merge($_POST, $body);
            } else {
                $action = $_POST['action'] ?? '';
            }

            error_log("Action: " . $action);
            error_log("POST data: " . print_r($_POST, true));

            try {
                switch ($action) {
                    case 'update_name':
                        $firstName = trim($_POST['f_name'] ?? '');
                        $lastName = trim($_POST['l_name'] ?? '');

                        if ($firstName === '' || $lastName === '') {
                            throw new Exception('First name and last name are required.');
                        }

                        $userModel->editUserName($userId, $firstName, $lastName);
                        $_SESSION['user_fName'] = $firstName;
                        $_SESSION['user_lName'] = $lastName;
                        $flash = ['type' => 'success', 'message' => 'Name updated successfully.'];
                        break;

                    case 'update_bio':
                        $bio = trim($_POST['bio'] ?? '');
                        $userModel->editBio($userId, $bio);
                        $flash = ['type' => 'success', 'message' => 'Bio updated successfully.'];
                        break;

                    case 'update_birthday':
                        $birthday = trim($_POST['birthday'] ?? '');
                        if ($birthday === '') {
                            throw new Exception('Birthday is required.');
                        }

                        $userModel->editBirthday($userId, $birthday);
                        $flash = ['type' => 'success', 'message' => 'Birthday updated successfully.'];
                        break;

                    case 'update_profile_picture':
                        if (empty($_FILES['profile_picture'])) {
                            throw new Exception('Please select an image.');
                        }

                        $profilePictureUrl = uploadImageToCloudinary($_FILES['profile_picture'], 'uniconnect_profile');

                        if (!$profilePictureUrl) {
                            throw new Exception('Profile picture upload failed.');
                        }

                        $userModel->editProfilePicture($userId, $profilePictureUrl);
                        $_SESSION['user_profilePicture'] = $profilePictureUrl;
                        $flash = ['type' => 'success', 'message' => 'Profile picture updated successfully.'];
                        break;

                    case 'update_password':
                        $currentPassword = $_POST['current_password'] ?? '';
                        $newPassword = $_POST['new_password'] ?? '';
                        $confirmPassword = $_POST['confirm_password'] ?? '';

                        if ($newPassword === '' || $confirmPassword === '') {
                            throw new Exception('New password and confirm password are required.');
                        }

                        if ($newPassword !== $confirmPassword) {
                            throw new Exception('New password and confirm password do not match.');
                        }

                        $currentUser = $userModel->first(['id' => $userId]);
                        if (!$currentUser || !password_verify($currentPassword, $currentUser->password)) {
                            throw new Exception('Current password is incorrect.');
                        }

                        $userModel->updatePassword($userId, $newPassword);
                        $flash = ['type' => 'success', 'message' => 'Password updated successfully.'];
                        break;

                    default:
                        throw new Exception('Invalid settings action: ' . $action);
                }

                ob_end_clean();
                echo json_encode(['success' => true, 'message' => $flash['message']]);
                exit;
            } catch (Exception $e) {
                ob_end_clean();
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                exit;
            }
        }

        // GET - render the view
        ob_end_clean();
        $user = $userModel->first(['id' => $userId]);

        $this->view('settings', [
            'title' => 'Settings | UniConnect',
            'head' => '
            <link rel="stylesheet" href="/assets/css/pages/settings.css">
            <link rel="stylesheet" href="/assets/css/components/navbar.css">
            <link rel="stylesheet" href="/assets/css/components/navPanel.css">
            ',
            'user' => $user,
            'flash' => $flash,
        ]);
    }
}
