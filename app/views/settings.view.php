<?php component("navbar"); ?>

<div class="settings-layout">
    <?php component("navPanel"); ?>

    <div class="settings-content">
        <h2>Edit Profile</h2>

        <?php if (!empty($flash['message'])): ?>
            <div class="settings-alert <?= htmlspecialchars($flash['type']) ?>">
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?>

        <div class="settings-grid">
            <section class="settings-card" data-section="profile_picture">
                <h3>Profile Picture</h3>

                <form class="settings-form" method="POST" enctype="multipart/form-data"
                    data-action="update_profile_picture">
                    <div class="current-avatar">
                        <?php if (!empty($user->profile_picture)): ?>
                            <img class="profile-image" src="<?= htmlspecialchars($user->profile_picture) ?>"
                                alt="Profile picture">
                        <?php else: ?>
                            <?php
                            $firstInitial = strtoupper(substr($user->f_name ?? '', 0, 1));
                            $lastInitial = strtoupper(substr($user->l_name ?? '', 0, 1));
                            ?>
                            <span class="profile"><?= htmlspecialchars($firstInitial . $lastInitial) ?></span>
                        <?php endif; ?>
                    </div>

                    <input type="file" name="profile_picture" accept="image/*" disabled>

                    <div class="settings-actions">
                        <button type="button" class="settings-edit-btn" data-edit-label="Edit Profile Picture">Edit
                            Profile Picture</button>
                        <button type="button" class="settings-cancel-btn" hidden>Cancel</button>
                        <button type="submit" class="settings-save-btn" hidden>Update</button>
                    </div>
                </form>
            </section>

            <section class="settings-card" data-section="name">
                <h3>Name</h3>

                <form class="settings-form" method="POST" data-action="update_name">
                    <label>First Name</label>
                    <input type="text" name="f_name" value="<?= htmlspecialchars($user->f_name ?? '') ?>" readonly>

                    <label>Last Name</label>
                    <input type="text" name="l_name" value="<?= htmlspecialchars($user->l_name ?? '') ?>" readonly>

                    <div class="settings-actions">
                        <button type="button" class="settings-edit-btn" data-edit-label="Edit Name">Edit Name</button>
                        <button type="button" class="settings-cancel-btn" hidden>Cancel</button>
                        <button type="submit" class="settings-save-btn" hidden>Update</button>
                    </div>
                </form>
            </section>

            <section class="settings-card" data-section="bio">
                <h3>Bio</h3>

                <form class="settings-form" method="POST" data-action="update_bio">
                    <textarea name="bio" rows="4" placeholder="Write your bio..."
                        readonly><?= htmlspecialchars($user->bio ?? '') ?></textarea>

                    <div class="settings-actions">
                        <button type="button" class="settings-edit-btn" data-edit-label="Edit Bio">Edit Bio</button>
                        <button type="button" class="settings-cancel-btn" hidden>Cancel</button>
                        <button type="submit" class="settings-save-btn" hidden>Update</button>
                    </div>
                </form>
            </section>

            <section class="settings-card" data-section="birthday">
                <h3>Birthday</h3>

                <form class="settings-form" method="POST" data-action="update_birthday">
                    <input type="date" name="birthday" value="<?= htmlspecialchars($user->birthday ?? '') ?>" readonly>

                    <div class="settings-actions">
                        <button type="button" class="settings-edit-btn" data-edit-label="Edit Birthday">Edit
                            Birthday</button>
                        <button type="button" class="settings-cancel-btn" hidden>Cancel</button>
                        <button type="submit" class="settings-save-btn" hidden>Update</button>
                    </div>
                </form>
            </section>
        </div>
    </div>

    <div class="settings-content">
        <h2>Change Password</h2>

        <div class="settings-grid">
            <section class="settings-card" data-section="password">
                <h3>Password</h3>

                <form class="settings-form" method="POST" data-action="update_password">
                    <label>Current Password</label>
                    <input type="password" name="current_password" readonly>

                    <label>New Password</label>
                    <input type="password" name="new_password" readonly>

                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password" readonly>

                    <div class="settings-actions">
                        <button type="button" class="settings-edit-btn" data-edit-label="Edit Password">Edit
                            Password</button>
                        <button type="button" class="settings-cancel-btn" hidden>Cancel</button>
                        <button type="submit" class="settings-save-btn" hidden>Update</button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>

<script src="/assets/js/settings.js"></script>