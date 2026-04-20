<?php $events = $events ?? []; ?>

<div class="events-table-wrapper">
    <div class="events-table-header">
        <h2>Events</h2>
    </div>

    <table class="events-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>University</th>
                <th>Held At</th>
                <th>Event Time</th>
                <th>Participants</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="events-table-body">
            <?php foreach ($events as $event): ?>
                <tr>
                    <td><?= htmlspecialchars($event->id) ?></td>
                    <td><?= htmlspecialchars($event->title) ?></td>
                    <td><?= htmlspecialchars($event->university_name ?? '-') ?></td>
                    <td><?= htmlspecialchars($event->held_at ?? '-') ?></td>
                    <td><?= htmlspecialchars(date('M d, Y H:i', strtotime($event->event_timestamp))) ?></td>
                    <td><?= htmlspecialchars($event->participant_count ?? 0) ?></td>
                    <td>
                        <button class="table-btn remove-btn"
                            onclick="removeEvent('<?= htmlspecialchars($event->id) ?>', this)">
                            Remove
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>