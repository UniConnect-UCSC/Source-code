<?php
require_once(__DIR__ . "/../../models/Friendship.php");

$profileUser = $profileUser ?? null;
$targetUserId = $profileUser->id ?? ($_SESSION['user_id'] ?? null);

$friends = [];
if ($targetUserId) {
    $friendshipModel = new Friendship();
    $allFriends = $friendshipModel->getFriends($targetUserId) ?: [];
    $friends = array_slice($allFriends, 0, 6);
}
?>

<div class="friends-widget">
    <div>
        <h2>Friends</h2>
    </div>

    <?php if (empty($friends)): ?>
        <p class="no-friends-message">No friends yet</p>
    <?php else: ?>
        <div class="friends-grid">
            <?php foreach ($friends as $friendship): ?>
                <?php $friend = $friendship->friend_details ?? null; ?>
                <?php if (!$friend) continue; ?>

                <?php
                $friendId = $friend['id'] ?? '';
                $firstName = $friend['firstname'] ?? '';
                $lastName = $friend['lastname'] ?? '';
                $fullName = trim($firstName . ' ' . $lastName);
                $profilePicture = $friend['profile_picture'] ?? '';
                ?>

                <a class="friend" href="/users/<?= htmlspecialchars($friendId) ?>">
                    <?php if (!empty($profilePicture)): ?>
                        <img src="<?= htmlspecialchars($profilePicture) ?>"
                            alt="<?= htmlspecialchars($fullName ?: 'Friend') ?> profile picture" />
                    <?php else: ?>
                        <div class="profile">
                            <?= htmlspecialchars(strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1))) ?>
                        </div>
                    <?php endif; ?>
                    <p><?= htmlspecialchars($fullName ?: 'Unknown User') ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <a class="see-all-friends" href="/friends">
        See All
    </a>
</div>