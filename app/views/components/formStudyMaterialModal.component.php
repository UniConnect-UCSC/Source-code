<!-- Add Material Modal -->
<div class="sm-modal" id="sm-add-modal" aria-hidden="true">
    <div class="sm-modal-backdrop" data-close-modal></div>
    <div class="sm-modal-content sm-add-modal-content" role="dialog" aria-modal="true" aria-labelledby="sm-add-title">
        <div class="sm-modal-header">
            <h3 id="sm-add-title">Add study material</h3>
            <button class="sm-modal-close" data-close-modal>&times;</button>
        </div>
        <form id="sm-upload-form" class="sm-upload-form">
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
            </div>

            <div class="sm-form-row">
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

            <div class="sm-form-actions">
                <button type="button" class="btn btn-secondary" id="sm-clear-btn">Clear</button>
                <button type="submit" class="btn btn-primary" id="sm-upload-btn">Upload</button>
            </div>
        </form>
    </div>
</div>
