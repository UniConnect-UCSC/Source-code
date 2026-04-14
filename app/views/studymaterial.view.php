<?php component('formStudyMaterialModal'); ?>
<?php component('manageStudyMaterialsModal'); ?>

<?php component("navbar"); ?>

<div class="home-layout">

    <?php component("navPanel"); ?>

    <div class="feed">
        <!-- Page Title -->
        <header>
            <h1>Study Materials</h1>
            <p class="subtitle">Browse, filter, and manage study resources shared across the community</p>
        </header>

        <!-- Controls: Search, Sort, and Management Buttons -->
        <div class="controls">
            <div class="controls-top">
                <div class="search-container">
                    <div class="search-input-wrapper" id="smSearchInputWrapper">
                        <input type="text" class="search-input" id="sm-search-input" placeholder="Search study materials...">
                        <i class="search-icon" data-lucide="search"></i>
                    </div>
                </div>

                <div class="sort-controls">
                    <label for="sm-sort-select" class="sm-sort-label">Sort by</label>
                    <select id="sm-sort-select" class="sm-sort-select">
                        <option value="recent" selected>Recent</option>
                        <option value="popular">Popular</option>
                        <option value="title">Title A–Z</option>
                    </select>
                </div>

            </div>

            <div class='btn-wrapper'>
                <button class="btn btn-primary" id="sm-add-btn">Create Study Material</button>
                <button class="btn btn-primary" id="sm-manage-btn">Manage Study Materials</button>
            </div>
        </div>

        <script>lucide.createIcons();</script>

        <!-- Feed: All Study Materials -->
        <div class="sm-grid" id="sm-grid"></div>
    </div>

    <?php component("widgetPanel"); ?>

</div>

<!-- Page scripts -->
<script src="/assets/js/studyMaterial/rendererModules.js"></script>
<script src="/assets/js/studyMaterial/scrollable.js"></script>
<script src="/assets/js/studyMaterial/mainCard.js"></script>
<script src="/assets/js/studyMaterial/form.js"></script>
<script src="/assets/js/studyMaterial/manage.js"></script>

<script src="/assets/js/studyMaterial/studyMaterial.js"></script>
