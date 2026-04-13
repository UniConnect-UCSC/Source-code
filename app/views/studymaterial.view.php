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
        <div class="sm-grid" id="sm-grid">
                    <!-- Card 1 -->
                    <article class="sm-material-card" data-type="document" data-topic="ai-ml" data-views="1200" data-ts="2025-09-22T10:00:00Z">
                        <div class="sm-material-icon doc">PDF</div>
                        <div class="sm-material-content">
                            <h4 class="sm-material-title">Intro to Machine Learning</h4>
                            <div class="sm-item-meta">UCSC • Document • 1.2K views</div>
                            <div class="sm-item-meta-2">
                                <small class="topic-pill" data-topic="ai-ml">AI/ML</small>
                            </div>
                            <div class="sm-item-tags">
                                <span class="tag">ML</span>
                                <span class="tag">Basics</span>
                            </div>
                        </div>
                    </article>
                    <!-- Card 2 -->
                    <article class="sm-material-card" data-type="video" data-topic="dsa" data-views="980" data-ts="2025-09-12T13:00:00Z">
                        <div class="sm-material-icon vid">VID</div>
                        <div class="sm-material-content">
                            <h4 class="sm-material-title">Gradient Descent Explained</h4>
                            <div class="sm-item-meta">UCSC • Video • 980 views</div>
                            <div class="sm-item-meta-2">
                                <small class="topic-pill" data-topic="dsa">DSA</small>
                            </div>
                            <div class="sm-item-tags">
                                <span class="tag">Optimization</span>
                                <span class="tag">Math</span>
                            </div>
                        </div>
                    </article>
                    <!-- Card 3 -->
                    <article class="sm-material-card" data-type="link" data-topic="sql" data-views="2400" data-ts="2025-08-18T09:00:00Z">
                        <div class="sm-material-icon lnk">URL</div>
                        <div class="sm-material-content">
                            <h4 class="sm-material-title">SQL Cheat Sheet</h4>
                            <div class="sm-item-meta">External Link • 2.4K views</div>
                            <div class="sm-item-meta-2">
                                <small class="topic-pill" data-topic="sql">SQL</small>
                            </div>
                            <div class="sm-item-tags">
                                <span class="tag">Reference</span>
                                <span class="tag">Quick Guide</span>
                            </div>
                        </div>
                    </article>
                    <!-- Card 4 -->
                    <article class="sm-material-card" data-type="document" data-topic="databases" data-views="520" data-ts="2025-07-03T08:00:00Z">
                        <div class="sm-material-icon doc">PDF</div>
                        <div class="sm-material-content">
                            <h4 class="sm-material-title">Past Paper 2022 - Algorithms</h4>
                            <div class="sm-item-meta">UCSC • Document • 520 views</div>
                            <div class="sm-item-meta-2">
                                <small class="topic-pill" data-topic="databases">Databases</small>
                            </div>
                            <div class="sm-item-tags">
                                <span class="tag">Past Paper</span>
                                <span class="tag">Algorithms</span>
                            </div>
                        </div>
                    </article>
        </div>
    </div>

    <?php component("widgetPanel"); ?>

</div>

<!-- Page scripts -->
<script src="/assets/js/studyMaterial/rendererModules.js"></script>
<script src="/assets/js/studyMaterial/scrollable.js"></script>
<script src="/assets/js/studyMaterial/form.js"></script>
<script src="/assets/js/studyMaterial/manage.js"></script>

<script src="/assets/js/studyMaterial/studyMaterial.js"></script>
