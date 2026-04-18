<?php

class User
{
    use Model;
    protected $table = 'users';
    //protected $allowedColumns = ['name', 'age'];

    public function getUniId($user_id)
    {
        $user = $this->where(conditions: [['id', '=', $user_id]], limit: 1);
        return $user ? $user[0]->university_id : null;
    }
    public function getUserNameById($user_id)
    {
        $user = $this->where(conditions: [['id', '=', $user_id]], limit: 1);
        return $user ? $user[0]->f_name . ' ' . $user[0]->l_name  : null;
    }
    public function getUserUniversityById($user_id)
    {
        $user = $this->where([['id', '=', $user_id]], 1);
        return $user && isset($user[0]->university_id) ? $user[0]->university_id : null;
    }
    public function getUserUniversityNameById($user_id)
    {
        $user = $this->where([['id', '=', $user_id]], 1);
        if ($user && isset($user[0]->university_id)) {
            $universityModel = new University();
            return $universityModel->getUniversityName($user[0]->university_id);
        }
        return null;
    }

    public function searchUsers(string $query, string $currentUserId, int $limit = 20): array
    {
        $query = trim($query);
        if ($query === '') {
            return [];
        }

        $sql = "
    SELECT
        u.id,
        u.f_name,
        u.l_name,
        u.email,
        u.university_id,
        u.profile_picture,
        uni.name AS university_name
    FROM users u
    LEFT JOIN universities uni ON uni.id = u.university_id
    WHERE u.id != :current_user_id
      AND (
          u.f_name ILIKE :q
          OR u.l_name ILIKE :q
          OR (u.f_name || ' ' || u.l_name) ILIKE :q
          OR u.email ILIKE :q
      )
    ORDER BY u.f_name ASC, u.l_name ASC
    LIMIT {$limit}
";

        $rows = $this->query($sql, [
            'current_user_id' => $currentUserId,
            'q' => '%' . $query . '%',
        ]);

        return $rows ?: [];
    }

    public function editUserName($userId, $firstName, $lastName)
    {
        return $this->update($userId, [
            'f_name' => $firstName,
            'l_name' => $lastName,
        ]);
    }

    public function editProfilePicture($userId, $profilePictureUrl)
    {
        return $this->update($userId, [
            'profile_picture' => $profilePictureUrl,
        ]);
    }

    public function editBio($userId, $bio)
    {
        return $this->update($userId, [
            'bio' => $bio,
        ]);
    }

    public function editBirthday($userId, $birthday)
    {
        return $this->update($userId, [
            'birthday' => $birthday,
        ]);
    }

    public function updatePassword($userId, $newPassword)
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->update($userId, [
            'password' => $hashedPassword,
        ]);
    }

    public function getTotalUsers()
    {
        return (int) $this->count([]);
    }

    public function getTotalActiveUsers() {}
}
