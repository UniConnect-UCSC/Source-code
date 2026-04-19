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
                    <td><?= htmlspecialchars($item->status ?? '-') ?></td>
                    <td>
                        <?php
                        $kuppiDateTime = $item->kuppi_date_time ?? null;
                        echo htmlspecialchars($kuppiDateTime ? date('M d, Y H:i', strtotime($kuppiDateTime)) : '-');
                        ?>
                    </td>
                    <td><?= htmlspecialchars($item->participants ?? 0) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>