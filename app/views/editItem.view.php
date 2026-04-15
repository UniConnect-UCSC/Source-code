<?php component("navbar"); ?>
<div class="feed">
  <div class="edit-item-container">
    <div class="edit-item-header">
      <h2>Edit Item</h2>
    </div>

    <div class="edit-item-form-container">
      <form class="edit-item-form"
            action="/marketplace/editItem/<?= htmlspecialchars($item->id) ?>"
            method="POST"
            enctype="multipart/form-data">

        <label for="title">Item Name:</label>
        <input type="text" id="title" name="title" value="<?= htmlspecialchars($item->title) ?>" required><br>
        <div id="edit-item-title-error" class="error-message"></div>

        <label for="description">Description:</label>
        <textarea id="description" name="description" required><?= htmlspecialchars($item->description) ?></textarea><br>
        <div id="edit-item-description-error" class="error-message"></div>

        <label for="price">Price:</label>
        <input type="number" id="price" name="price" step="0.01" value="<?= htmlspecialchars($item->price) ?>" required><br>
        <div id="edit-item-price-error" class="error-message"></div>

        <label for="status">Status:</label>
        <select id="status" name="status" required>
          <?php foreach ($statusOptions as $status): ?>
            <option value="<?= htmlspecialchars($status) ?>" <?= $item->status === $status ? 'selected' : '' ?>>
              <?= ucwords(str_replace('_', ' ', $status)) ?>
            </option>
          <?php endforeach; ?>
        </select><br>
        <div id="edit-item-status-error" class="error-message"></div>

        <label for="category_id">Category:</label>
<select id="category_id" name="category_id" required>
  <?php foreach (($categories ?? []) as $cat): ?>
    <option value="<?= htmlspecialchars($cat->id) ?>"
      <?= (int)$item->category_id === (int)$cat->id ? 'selected' : '' ?>>
      <?= htmlspecialchars($cat->name) ?>
    </option>
  <?php endforeach; ?>
</select><br>
<div id="edit-item-category-error" class="error-message"></div>

<label for="contact_number">Contact Number:</label>
<input type="text" id="contact_number" name="contact_number" value="<?= htmlspecialchars($item->contact_number) ?>" required><br>
<div id="edit-item-contact-number-error" class="error-message"></div>

        <label for="image">Upload New Images:</label>
          <input type="file" id="image" name="images[]" accept="image/*" multiple><br>
          <div id="edit-item-image-error" class="error-message"></div>

                <?php if (!empty($currentImages)): ?>
  <div class="current-image">
    <p>Current Images:</p>
    <div class="current-image-grid">
      <?php foreach ($currentImages as $image): ?>
        <div class="current-image-item" data-image-id="<?= htmlspecialchars((string)$image['id']) ?>">
          <img src="<?= htmlspecialchars($image['url']) ?>" alt="Current item image">
          <button type="button" class="remove-current-image-btn" data-image-id="<?= htmlspecialchars((string)$image['id']) ?>" aria-label="Remove image">&times;</button>
        </div>
      <?php endforeach; ?>
    </div>
    <div id="remove-image-ids-container"></div>
  </div>
<?php endif; ?>

        <div class="form-actions">
          <button type="submit" class="btn submit-btn">Update Item</button>
          <a href="/marketplace/myItems" class="btn cancel-btn">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="/assets/js/pages/editItem.js"></script>