<?php component("navbar");?>
<div class="request-kuppi-container">
    <div class="navbar">
        <?php component("navbar");?>
    </div>
    <div class="request-kuppi-form">
        <h2>Request a kuppi</h2>
        <form action="kuppi/request_kuppi" method="POST">
            <label for="Name">Name</label>
            <input type="text" id="Name" name="name" required>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
            <label for="category">Category</label>
            <select name="category_id" id="category" required>
                <?php foreach ($kuppiCategories as $category): ?>
                    <option value="<?= $category->id ?>">
                        <?= htmlspecialchars($category->category_name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <label for="subject">Subject:</label>
            <input type="text" id="subject" name="subject" required>
            <label name="isAnonymous">Request Anonymously</label>
            <div class="checkbox-container">
                <input type="checkbox" id="isAnonymous" name="isAnonymous" value="1">
                <label for="isAnonymous">Yes, I want to request anonymously</label>     
            </div>
            <button type="submit">Submit Request</button>
        </form>
    </div>
    <div class="widget-panel">
        <?php component("widgetPanel"); ?>
    </div>
</div>          

