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
                    <article class="sm-material-card" data-type="document" data-subject="ai-ml" data-views="1200" data-ts="2025-09-22T10:00:00Z">
                        <div class="sm-material-header">
                            <div class="sm-type-badge doc">PDF</div>
                        </div>
                        <div class="sm-material-body">
                            <h4 class="sm-material-title">Intro to Machine Learning</h4>
                            <span class="sm-subject-tag">AI/ML</span>
                            <p class="sm-material-description">Comprehensive introduction to machine learning concepts, algorithms, and practical applications including supervised and unsupervised learning techniquesComprehensive introduction to machine learning concepts, algorithms, and practical applications including supervised and unsupervised learning techniquesComprehensive introduction to machine learning concepts, algorithms, and practical applications including supervised and unsupervised learning techniquesComprehensive introduction to machine learning concepts, algorithms, and practical applications including supervised and unsupervised learning techniques.</p>
                        </div>
                        <div class="sm-material-footer">
                            <div class="sm-footer-items">
                                <div class="sm-footer-item">
                                    <i data-lucide="eye" class="sm-footer-icon"></i>
                                    <span class="sm-footer-text">1.2K views</span>
                                </div>
                                <div class="sm-footer-item">
                                    <i data-lucide="calendar" class="sm-footer-icon"></i>
                                    <span class="sm-footer-text">Sep 22, 2025</span>
                                </div>
                            </div>
                            <button class="sm-view-btn" type="button">View</button>
                        </div>
                    </article>

                    <!-- Card 2 -->
                    <article class="sm-material-card" data-type="video" data-subject="dsa" data-views="980" data-ts="2025-09-12T13:00:00Z">
                        <div class="sm-material-header">
                            <div class="sm-type-badge vid">VIDEO</div>
                        </div>
                        <div class="sm-material-body">
                            <h4 class="sm-material-title">Gradient Descent Explained</h4>
                            <span class="sm-subject-tag">Data Structures & Algorithms</span>
                            <p class="sm-material-description">Deep dive into gradient descent optimization algorithm with visual explanations, mathematical derivations, and real-world optimization examples.</p>
                        </div>
                        <div class="sm-material-footer">
                            <div class="sm-footer-items">
                                <div class="sm-footer-item">
                                    <i data-lucide="eye" class="sm-footer-icon"></i>
                                    <span class="sm-footer-text">980 views</span>
                                </div>
                                <div class="sm-footer-item">
                                    <i data-lucide="calendar" class="sm-footer-icon"></i>
                                    <span class="sm-footer-text">Sep 12, 2025</span>
                                </div>
                            </div>
                            <button class="sm-view-btn" type="button">View</button>
                        </div>
                    </article>

                    <!-- Card 3 -->
                    <article class="sm-material-card" data-type="link" data-subject="sql" data-views="2400" data-ts="2025-08-18T09:00:00Z">
                        <div class="sm-material-header">
                            <div class="sm-type-badge lnk">LINK</div>
                        </div>
                        <div class="sm-material-body">
                            <h4 class="sm-material-title">SQL Cheat Sheet</h4>
                            <span class="sm-subject-tag">SQL</span>
                            <p class="sm-material-description">Quick reference guide for SQL queries, syntax, and best practices. Includes examples for SELECT, JOIN, aggregation, and window functions.</p>
                        </div>
                        <div class="sm-material-footer">
                            <div class="sm-footer-items">
                                <div class="sm-footer-item">
                                    <i data-lucide="eye" class="sm-footer-icon"></i>
                                    <span class="sm-footer-text">2.4K views</span>
                                </div>
                                <div class="sm-footer-item">
                                    <i data-lucide="calendar" class="sm-footer-icon"></i>
                                    <span class="sm-footer-text">Aug 18, 2025</span>
                                </div>
                            </div>
                            <button class="sm-view-btn" type="button">View</button>
                        </div>
                    </article>

                    <!-- Card 4 -->
                    <article class="sm-material-card" data-type="document" data-subject="algorithms" data-views="520" data-ts="2025-07-03T08:00:00Z">
                        <div class="sm-material-header">
                            <div class="sm-type-badge doc">PDF</div>
                        </div>
                        <div class="sm-material-body">
                            <h4 class="sm-material-title">Past Paper 2022 - Algorithms</h4>
                            <span class="sm-subject-tag">Algorithms</span>
                            <p class="sm-material-description">Complete past examination paper from 2022 covering algorithm design, complexity analysis, sorting techniques, and graph algorithms.</p>
                        </div>
                        <div class="sm-material-footer">
                            <div class="sm-footer-items">
                                <div class="sm-footer-item">
                                    <i data-lucide="eye" class="sm-footer-icon"></i>
                                    <span class="sm-footer-text">520 views</span>
                                </div>
                                <div class="sm-footer-item">
                                    <i data-lucide="calendar" class="sm-footer-icon"></i>
                                    <span class="sm-footer-text">Jul 3, 2025</span>
                                </div>
                            </div>
                            <button class="sm-view-btn" type="button">View</button>
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
