<!-- Add Material Modal -->
<div class="modal" id="formStudyMaterialModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="sm-add-title">Add study material</h2>
            <button class="close-btn" id="closeFromModalBtn">&times;</button>
        </div>
        <form id="sm-upload-form">
            <div class="form-group">
                <label for="sm-title">Title<span class="req">*</span></label>
                <input type="text" id="sm-title" name="title" placeholder="e.g., Introduction to Machine Learning" required>
            </div>

            <!-- This should be handled through a scroll element with a table with current subjects available -->
            <div class="form-group">
                <label for="sm-subject">Subject / Course</label>
                <input type="text" id="sm-subject" name="subject" placeholder="e.g., CS3100 Machine Learning">
            </div>

            <div class="form-group">
                <label for="sm-type">Type</label>
                <select id="sm-type" name="type">
                    <option value="document" selected>Document (PDF, PPT, DOC)</option>
                    <option value="video">Video</option>
                    <option value="link">External Link</option>
                </select>
            </div>
            <div class="form-group" id="sm-file-field">
                <label for="sm-file">File</label>
                <input type="file" id="sm-file" name="file" accept=".pdf,.ppt,.pptx,.doc,.docx,.zip,.rar,.mp4,.mov">
                <small class="help-text">Max 50MB. PDF/PPT/DOC or MP4/MOV for videos</small>
            </div>
            <div class="form-group hidden" id="sm-link-field">
                <label for="sm-link">URL</label>
                <input type="url" id="sm-link" name="link" placeholder="https://...">
            </div>
            <div class="form-group">
                <label for="sm-description">Description</label>
                <textarea id="sm-description" name="description" rows="3" placeholder="Add a short description or notes (optional)"></textarea>
            </div>


            <!-- This should be handled through a scroll element with a table with current subjects available -->
            <div class="form-group">
                <label for="sm-tags">Tags</label>
                <input type="text" id="sm-tags" name="tags" placeholder="e.g., basics, regression, lecture-01 (comma separated)">
            </div>

            <button type="submit" class="btn btn-primary" id="sm-upload-btn" style="width: 100%;">Confirm</button>
        </form>
    </div>
</div>
