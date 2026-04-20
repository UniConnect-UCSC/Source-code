<div class="users-table-wrapper">
    <div class="users-table-header">
        <h2>Users</h2>
        <input type="text" id="user-search" placeholder="Search users..." class="user-search-input">
    </div>

    <table class="users-table">
        <thead>
            <tr>
                <th>User</th>
                <th>Email</th>
                <th>Joined</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="users-table-body">
            <?php foreach ($users as $user): ?>
                <?php $isBanned = !empty($user->deleted_at); ?>
                <tr>
                    <td>
                        <div class="user-cell">
                            <?php if (!empty($user->profile_picture)): ?>
                                <img class="user-avatar" src="<?= htmlspecialchars($user->profile_picture) ?>" alt="avatar">
                            <?php else: ?>
                                <div class="user-avatar-initials">
                                    <?= strtoupper(substr($user->f_name ?? '', 0, 1)) ?>
                                    <?= strtoupper(substr($user->l_name ?? '', 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                            <span><?= htmlspecialchars($user->f_name . ' ' . $user->l_name) ?></span>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($user->email) ?></td>
                    <td><?= htmlspecialchars(date('M d, Y', strtotime($user->account_created_at))) ?></td>
                    <td>
                        <button class="table-btn view-btn"
                            onclick="viewUser('<?= htmlspecialchars($user->id) ?>')">View</button>
                        <button class="table-btn ban-btn"
                            onclick="toggleBanStatus('<?= htmlspecialchars($user->id) ?>', <?= $isBanned ? 'true' : 'false' ?>, this)">
                            <?= $isBanned ? 'Unban' : 'Ban' ?>
                        </button>

                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div id="users-scroll-anchor" style="height: 1px;"></div>
</div>