<?php component("navbar"); ?>
<div class="home-layout">
    <?php component("navPanel"); ?>
    <div class="feed">
        <div class="feed-header">
            <h2>Sell an Item</h2>
        </div>
        <div class="sell-item-form-container">
            <form action="/marketplace/createItem" method="POST" enctype="multipart/form-data" class="sell-item-form">
                <label for="title">Item Name:</label>
                <input type="text" id="title" name="title" required><br>
                
                <label for="category_id">Category </label>
                <select id="category_id" name="category_id" required>
                    <?php foreach ($categories as $category): ?>
                    <option value="<?= $category->id ?>">
                        <?= htmlspecialchars($category->name) ?>
                    </option>
                    <?php endforeach; ?>
                </select><br>

                <label for="description">Description:</label>
                <input type="text" id="description" name="description" required></textarea><br>

                <label for="price">Price:</label>
                <input type="number" id="price" name="price" step="0.01" required><br>

                <label for="image">Upload Image:</label>
                <input type="file" id="image" name="image" accept="image/*"><br>

                <button type="submit" class="btn submit-btn">Submit</button>
            </form>
        </div>
    </div>
<?php component("widgetPanel"); ?>
</div>