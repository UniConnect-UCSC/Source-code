<?php $kuppi = $kuppi ?? []; ?>

<div class="kuppi-table-wrapper">
    <div class="kuppi-table-header">
        <h2>Reported Kuppi</h2>
    </div>

    <table class="kuppi-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Topic</th>
                <th>Host</th>
                <th>University</th>
                <th>Category</th>
                <th>Reports</th>
                <th>Last Reported</th>
                <th>Report Status</th>
                <th>Decision</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="kuppi-table-body">
            <?php foreach ($kuppi as $item): ?>
                <?php
                $reportStatus = trim((string)($item->report_status ?? 'Pending'));
                $reportStatusSlug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $reportStatus));
                $decision = trim((string)($item->decision ?? ''));
                $decisionSlug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $decision));
                ?>
                <tr>
                    <td><?= htmlspecialchars($item->id) ?></td>
                    <td><?= htmlspecialchars($item->topic ?? '-') ?></td>
                    <td><?= htmlspecialchars(trim(($item->host_f_name ?? '') . ' ' . ($item->host_l_name ?? ''))) ?></td>
                    <td><?= htmlspecialchars($item->host_university ?? '-') ?></td>
                    <td><?= htmlspecialchars($item->category_name ?? '-') ?></td>
                    <td><?= htmlspecialchars((string)($item->report_count ?? 0)) ?></td>
                    <td><?= htmlspecialchars(!empty($item->last_reported_at) ? date('M d, Y H:i', strtotime($item->last_reported_at)) : '-') ?>
                    </td>
                    <td class="report-status-cell">
                        <span class="kuppi-status-pill status-<?= htmlspecialchars($reportStatusSlug ?: 'unknown') ?>">
                            <?= htmlspecialchars($reportStatus) ?>
                        </span>
                    </td>
                    <td class="decision-cell">
                        <?php if ($decision !== ''): ?>
                            <span class="kuppi-status-pill status-<?= htmlspecialchars($decisionSlug) ?>">
                                <?= htmlspecialchars($decision) ?>
                            </span>
                        <?php else: ?>
                            <span>-</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($reportStatus === 'Pending'): ?>
                            <button class="table-btn allow-btn"
                                onclick="resolveKuppiReport('<?= htmlspecialchars($item->id) ?>', 'approve', this)">
                                Allow
                            </button>
                            <button class="table-btn reject-btn"
                                onclick="resolveKuppiReport('<?= htmlspecialchars($item->id) ?>', 'reject', this)">
                                Reject
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