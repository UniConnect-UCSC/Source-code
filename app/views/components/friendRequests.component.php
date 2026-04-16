<?php $friendRequests = $friendRequests ?? []; ?>

<div class="friend-requests-container">
    <div class="friend-requests-header">Friend Requests</div>

    <div class="friend-requests">
        <?php if (empty($friendRequests)): ?>
            <div class="friend-requests-empty-card">
                <h3 class="friend-requests-empty-title">No pending requests</h3>
                <p class="friend-requests-empty-text">
                    You do not have any friend requests right now. New requests will appear here.
                </p>
            </div>
        <?php else: ?>
            <?php foreach ($friendRequests as $request): ?>
                <div class="friend-request-card">
                    <a href="/users/<?php echo htmlspecialchars($request['id']); ?>">
                        <?php if (!empty($request['profile_picture'])): ?>
                            <img src="<?php echo htmlspecialchars($request['profile_picture']); ?>"
                                alt="<?php echo htmlspecialchars(($request['firstname'] ?? '') . ' ' . ($request['lastname'] ?? '')); ?>"
                                class="friend-request-profile-picture">
                        <?php else: ?>
                            <div class="default-avatar">
                                <span><?php echo strtoupper(substr($request['firstname'] ?? '', 0, 1)); ?></span>
                                <span><?php echo strtoupper(substr($request['lastname'] ?? '', 0, 1)); ?></span>
                            </div>
                        <?php endif; ?>
                    </a>

                    <div class="friend-request-info">
                        <h3 class="friend-request-name">
                            <?php echo htmlspecialchars(($request['firstname'] ?? '') . ' ' . ($request['lastname'] ?? '')); ?>
                        </h3>
                        <p class="friend-request-university">
                            <?php echo htmlspecialchars($request['university'] ?? 'University not set'); ?>
                        </p>

                        <div class="friend-request-actions">
                            <button class="accept-friend-request" type="button"
                                onclick="acceptFriendRequest(this, '<?php echo htmlspecialchars($request['id']); ?>')">Accept</button>
                            <button class="decline-friend-request" type="button"
                                onclick="declineFriendRequest(this, '<?php echo htmlspecialchars($request['id']); ?>')">Decline</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>