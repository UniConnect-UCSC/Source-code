<?php   component("navbar"); ?>
<!-- Create Kuppi Post -->
<div class="home-layout">

    <?php component("navPanel"); ?> 
    
    <div class="create-kuppi-post">
        <form action="/kuppi/create" method="POST">
        <div class="form-row">
            <label for="topic">Topic</label>
            <input type="text" id="topic" name="topic" placeholder="Add a topic" required>
        </div>
        <div class="form-row">
            <label for="date">Date</label>
            <input type="date" id="date" name="date" required>
        </div>
        <div class="form-row">
            <label for="time">Time</label>
            <input type="time" id="time" name="time" required>
        </div>
        <div class="form-row">
            <label for="platform">Platform</label>
            <select name="platform" id="platform" required>
                <option value="Zoom">Zoom</option>
                <option value="Google Meet">Google Meet</option>
                <option value="MS teams">MS teams</option>
            </select>
        </div>
        <div class="form-row">
            <label for="category">Category</label>
            <select name="category_id" id="category" required>
                <?php foreach ($kuppiCategories as $category): ?>
                <option value="<?= $category->id ?>">
                    <?= htmlspecialchars($category->category_name) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
    <button type="submit">Create Kuppi</button>
</form>
    </div>
        <?php component("widgetPanel"); ?>
</div>
