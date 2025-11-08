
<!-- filepath: c:\Users\sanda\OneDrive\Desktop\Group Project\uniconnect_docker\uniconnect_docker\Source-code\app\views\edit_kuppi.view.php -->
<?php component("navbar"); ?>
<div class="feed">
    <h2>Edit Kuppi Session</h2>
    <form action="/kuppi/edit_kuppi/<?= htmlspecialchars($kuppi->id) ?>" method="POST">
        <div class="form-row">
            <label for="topic">Topic</label>
            <input type="text" id="topic" name="topic" value="<?= htmlspecialchars($kuppi->topic) ?>" required>
        </div>
        <div class="form-row">
            <label for="date">Date</label>
            <input type="date" id="date" name="date" value="<?= date('Y-m-d', strtotime($kuppi->kuppi_date_time)) ?>" required>
        </div>
        <div class="form-row">
            <label for="time">Time</label>
            <input type="time" id="time" name="time" value="<?= date('H:i', strtotime($kuppi->kuppi_date_time)) ?>" required>
        </div>
        <div class="form-row">
            <label for="platform">Platform</label>
            <select name="platform" id="platform" required>
                <option value="Zoom" <?= $kuppi->platform == 'Zoom' ? 'selected' : '' ?>>Zoom</option>
                <option value="Google Meet" <?= $kuppi->platform == 'Google Meet' ? 'selected' : '' ?>>Google Meet</option>
                <option value="MS teams" <?= $kuppi->platform == 'MS teams' ? 'selected' : '' ?>>MS teams</option>
            </select>
        </div>
        <div class="form-row">
            <label for="category">Category</label>
            <select name="category_id" id="category" required>
                <?php foreach ($kuppiCategories as $category): ?>
                    <option value="<?= $category->id ?>" <?= $kuppi->category_id == $category->id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category->category_name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit">Update Kuppi</button>
    </form>
</div>