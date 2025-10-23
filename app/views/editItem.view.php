<?php component("navbar"); ?>
<div class="feed">
    <h2>Edit Item</h2>
    <form action="/marketplace/editItem/<?= htmlspecialchars($item->id) ?>" method="POST">
        <label for="title">Item Name:</label>
        <input type="text" id="title" name="title" value="<?= htmlspecialchars($item->title) ?>" required><br>

        <label for="description">Description:</label>
        <textarea id="description" name="description" required><?= htmlspecialchars($item->description) ?></textarea><br>

        <label for="price">Price:</label>
        <input type="number" id="price" name="price" step="0.01" value="<?= htmlspecialchars($item->price) ?>" required><br>

        <label for="status">Status:</label>
        <select id="status" name="status" required>
            <?php foreach ($statusOptions as $status): ?>
                <option value="<?= htmlspecialchars($status) ?>" <?= $item->status === $status ? 'selected' : '' ?>>
                     <?= ucwords(str_replace('_', ' ', $status)) ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <button type="submit" class="btn submit-btn">Update Item</button>
        <a href="/marketplace/myItems" class="btn cancel-btn">Cancel</a>
    </form>
</div>