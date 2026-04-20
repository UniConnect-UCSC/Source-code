<?php
$repRequests = $repRequests ?? [];
$uniReps = $uniReps ?? [];
?>

<div class="uni-rep-table-wrapper">
    <div class="uni-rep-table-header">
        <h2>University Representatives</h2>

        <div class="uni-rep-table-controls">
            <div class="uni-rep-toggle" role="tablist" aria-label="Uni Rep Type">
                <button type="button" class="uni-rep-toggle-btn active" data-type="pending">Pending Requests</button>
                <button type="button" class="uni-rep-toggle-btn" data-type="reps">Representatives</button>
            </div>
        </div>
    </div>

    <table class="uni-rep-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>University</th>
                <th>Person</th>
                <th>Proof</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="uni-rep-table-body">
            <?php foreach ($repRequests as $request): ?>
                <tr>
                    <td><?= htmlspecialchars($request->id) ?></td>
                    <td><?= htmlspecialchars($request->university_name ?? '-') ?></td>
                    <td><?= htmlspecialchars(trim(($request->student_f_name ?? '') . ' ' . ($request->student_l_name ?? ''))) ?>
                    </td>
                    <td>
                        <button class="table-btn view-btn"
                            onclick="viewProof('<?= htmlspecialchars($request->proof_url) ?>')">
                            View Proof
                        </button>
                    </td>
                    <td>
                        <span class="status-pill status-pending">Pending</span>
                    </td>
                    <td><?= htmlspecialchars(date('M d, Y', strtotime($request->requested_at))) ?></td>
                    <td>
                        <button class="table-btn accept-btn" onclick="approveRequest(<?= (int)$request->id ?>, this)">
                            Accept
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>