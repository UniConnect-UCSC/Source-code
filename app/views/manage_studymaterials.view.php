<?php component("navbar"); ?>

<div class="home-layout">

    <?php component("navPanel"); ?>

    <div class="feed">
        <header>
            <h1>Manage Study Materials</h1>
            <p class="subtitle">View, edit, and delete your uploaded materials</p>
        </header>

        <section class="sm-card">
            <div class="sm-card-body">
                <div class="controls">
                    <div class="controls-top">
                        <div class="search-container">
                            <button class="search-btn" id="smSearchToggleBtn">🔍</button>
                            <div class="search-input-wrapper" id="smSearchInputWrapper">
                                <input type="text" class="search-input" id="sm-search-input" placeholder="Search my materials...">
                            </div>
                        </div>
                        <button class="btn btn-primary" id="sm-add-btn">Create Study Material</button>
                    </div>

                    <div class="categories-wrapper" id="categoryWrapper">
                        <button class="category-btn active" data-category="all" id="allCategoriesBtn">All</button>
                        <div class="categories-section" id="categoriesSection">
                            <button class="category-btn" data-category="sql">SQL</button>
                            <button class="category-btn" data-category="dsa">DSA</button>
                            <button class="category-btn" data-category="databases">Databases</button>
                            <button class="category-btn" data-category="oop">OOP</button>
                            <button class="category-btn" data-category="os">OS</button>
                            <button class="category-btn" data-category="networks">Networks</button>
                            <button class="category-btn" data-category="ai-ml">AI/ML</button>
                            <button class="category-btn" data-category="web">Web</button>
                            <button class="category-btn" data-category="mobile">Mobile</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="sm-card">
            <div class="sm-card-header">
                <h3>My materials</h3>
            </div>
            <div class="sm-card-body">
                <ul class="sm-list" id="sm-manage-list">
                    <li class="sm-item" data-id="m1">
                        <div class="sm-item-main">
                            <div class="sm-item-icon doc">PDF</div>
                            <div class="sm-item-info">
                                <h4 class="sm-item-title">Intro to Machine Learning</h4>
                                <div class="sm-item-meta">Document • Uploaded Sep 22, 2025</div>
                            </div>
                        </div>
                        <div class="sm-item-actions">
                            <button class="btn btn-ghost" data-edit="m1">Edit</button>
                            <button class="btn btn-danger" data-delete="m1">Delete</button>
                        </div>
                    </li>
                    <li class="sm-item" data-id="m2">
                        <div class="sm-item-main">
                            <div class="sm-item-icon vid">VID</div>
                            <div class="sm-item-info">
                                <h4 class="sm-item-title">Linear Regression Deep Dive</h4>
                                <div class="sm-item-meta">Video • Uploaded Sep 12, 2025</div>
                            </div>
                        </div>
                        <div class="sm-item-actions">
                            <button class="btn btn-ghost" data-edit="m2">Edit</button>
                            <button class="btn btn-danger" data-delete="m2">Delete</button>
                        </div>
                    </li>
                </ul>
            </div>
        </section>
    </div>

    <?php component("widgetPanel"); ?>

</div>

<!-- Reuse Add/Edit/Delete Modals from main page for consistency -->
<div class="sm-modal" id="sm-add-modal" aria-hidden="true">
    <div class="sm-modal-backdrop" data-close-modal></div>
    <div class="sm-modal-content" role="dialog" aria-modal="true" aria-labelledby="sm-add-title">
        <div class="sm-modal-header">
            <h3 id="sm-add-title">Add study material</h3>
            <button class="sm-modal-close" data-close-modal>&times;</button>
        </div>
        <div class="sm-modal-body">
            <form id="sm-upload-form" class="sm-form" onsubmit="return false;">
                <div class="sm-form-row">
                    <div class="sm-field">
                        <label for="sm-title">Title<span class="req">*</span></label>
                        <input type="text" id="sm-title" name="title" placeholder="e.g., Introduction to Machine Learning" required>
                    </div>
                    <div class="sm-field">
                        <label for="sm-subject">Subject / Course</label>
                        <input type="text" id="sm-subject" name="subject" placeholder="e.g., CS3100 Machine Learning">
                    </div>
                </div>

                <div class="sm-form-row">
                    <div class="sm-field">
                        <label for="sm-topic">Category</label>
                        <select id="sm-topic" name="topic">
                            <option value="sql">SQL</option>
                            <option value="dsa">DSA</option>
                            <option value="databases">Databases</option>
                            <option value="oop">OOP</option>
                            <option value="os">OS</option>
                            <option value="networks">Networks</option>
                            <option value="ai-ml" selected>AI/ML</option>
                            <option value="web">Web</option>
                            <option value="mobile">Mobile</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="sm-field">
                        <label for="sm-type">Type</label>
                        <select id="sm-type" name="type">
                            <option value="document" selected>Document (PDF, PPT, DOC)</option>
                            <option value="video">Video</option>
                            <option value="link">External Link</option>
                        </select>
                    </div>
                    <div class="sm-field" id="sm-file-field">
                        <label for="sm-file">File</label>
                        <input type="file" id="sm-file" name="file" accept=".pdf,.ppt,.pptx,.doc,.docx,.zip,.rar,.mp4,.mov">
                        <small class="help-text">Max 50MB. PDF/PPT/DOC or MP4/MOV for videos</small>
                    </div>
                    <div class="sm-field hidden" id="sm-link-field">
                        <label for="sm-link">URL</label>
                        <input type="url" id="sm-link" name="link" placeholder="https://...">
                    </div>
                </div>

                <div class="sm-form-row">
                    <div class="sm-field sm-field-full">
                        <label for="sm-description">Description</label>
                        <textarea id="sm-description" name="description" rows="3" placeholder="Add a short description or notes (optional)"></textarea>
                    </div>
                </div>

                <div class="sm-form-row">
                    <div class="sm-field sm-field-full">
                        <label for="sm-tags">Tags</label>
                        <input type="text" id="sm-tags" name="tags" placeholder="e.g., basics, regression, lecture-01 (comma separated)">
                    </div>
                </div>

                <div class="sm-actions">
                    <button type="button" class="btn btn-secondary" id="sm-clear-btn">Clear</button>
                    <button type="submit" class="btn btn-primary" id="sm-upload-btn">Upload</button>
                </div>
            </form>
        </div>
        <div class="sm-modal-footer">
            <button class="btn btn-secondary" data-close-modal>Close</button>
        </div>
    </div>
</div>

<div class="sm-modal" id="sm-edit-modal" aria-hidden="true">
    <div class="sm-modal-backdrop" data-close-modal></div>
    <div class="sm-modal-content" role="dialog" aria-modal="true" aria-labelledby="sm-edit-title">
        <div class="sm-modal-header">
            <h3 id="sm-edit-title">Edit study material</h3>
            <button class="sm-modal-close" data-close-modal>&times;</button>
        </div>
        <div class="sm-modal-body">
            <form id="sm-edit-form" class="sm-form" onsubmit="return false;">
                <input type="hidden" id="sm-edit-id">
                <div class="sm-form-row">
                    <div class="sm-field sm-field-full">
                        <label for="sm-edit-title-input">Title</label>
                        <input type="text" id="sm-edit-title-input" required>
                    </div>
                </div>
                <div class="sm-form-row">
                    <div class="sm-field sm-field-full">
                        <label for="sm-edit-description">Description</label>
                        <textarea id="sm-edit-description" rows="3"></textarea>
                    </div>
                </div>
            </form>
        </div>
        <div class="sm-modal-footer">
            <button class="btn btn-secondary" data-close-modal>Cancel</button>
            <button class="btn btn-primary" id="sm-edit-save">Save changes</button>
        </div>
    </div>
</div>

<div class="sm-modal" id="sm-delete-modal" aria-hidden="true">
    <div class="sm-modal-backdrop" data-close-modal></div>
    <div class="sm-modal-content" role="dialog" aria-modal="true" aria-labelledby="sm-delete-title">
        <div class="sm-modal-header">
            <h3 id="sm-delete-title">Delete study material</h3>
            <button class="sm-modal-close" data-close-modal>&times;</button>
        </div>
        <div class="sm-modal-body">
            <p>Are you sure you want to delete this material? This action cannot be undone.</p>
        </div>
        <div class="sm-modal-footer">
            <button class="btn btn-secondary" data-close-modal>Cancel</button>
            <button class="btn btn-danger" id="sm-delete-confirm">Delete</button>
        </div>
    </div>
</div>

<script src="/assets/js/pages/studyMaterial.js"></script>
