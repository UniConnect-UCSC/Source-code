<?php $kuppi = $kuppi ?? []; ?>

<div class="kuppi-table-wrapper">
    <div class="kuppi-table-header">
        <h2>Kuppi</h2>
    </div>

    <table class="kuppi-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Topic</th>
                <th>Host</th>
                <th>University</th>
                <th>Category</th>
                <th>Status</th>
                <th>Time</th>
                <th>Participants</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="kuppi-table-body">
            <?php foreach ($kuppi as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item->id) ?></td>
                    <td><?= htmlspecialchars($item->topic ?? '-') ?></td>
                    <td><?= htmlspecialchars(trim(($item->host_f_name ?? '') . ' ' . ($item->host_l_name ?? ''))) ?></td>
                    <td><?= htmlspecialchars($item->university_name ?? '-') ?></td>
                    <td><?= htmlspecialchars($item->category_name ?? '-') ?></td>
                    <?php
                    $status = trim((string)($item->status ?? '-'));
                    $statusSlug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $status));
                    ?>
                    <td class="kuppi-status-cell">
                        <span class="kuppi-status-pill status-<?= htmlspecialchars($statusSlug ?: 'unknown') ?>">
                            <?= htmlspecialchars($status) ?>
                        </span>
                    </td>
                    <td>
                        <?php
                        $kuppiDateTime = $item->kuppi_date_time ?? null;
                        echo htmlspecialchars($kuppiDateTime ? date('M d, Y H:i', strtotime($kuppiDateTime)) : '-');
                        ?>
                    </td>
                    <td><?= htmlspecialchars($item->participants ?? 0) ?></td>
                    <td>
                        <?php if (($item->status ?? '') === 'Requested'): ?>
                            <button class="table-btn approve-btn"
                                onclick="approveKuppiRequest('<?= htmlspecialchars($item->id) ?>', this)">
                                Approve
                            </button>
                        <?php else: ?>
                            <span>-</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>