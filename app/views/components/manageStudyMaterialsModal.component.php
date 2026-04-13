<!-- Manage My Materials Modal -->
<div class="modal" id="manageStudyMaterialsModal">
    <div class="modal-content sm-manage-modal">
        <div class="modal-header">
            <h2>My study materials</h2>
            <button class="close-btn" id="closeStudyMaterialModalBtn">&times;</button>
        </div>
            <div class="sm-manage-materials">
                <div class="sm-manage-header no-select">
                    <div>Material</div>
                    <div>Type</div>
                    <div>Category</div>
                    <div>Uploaded</div>
                    <div class="text-right">Actions</div>
                </div>

                <div class="sm-manage-body">
                    <div class="sm-manage-list" id="sm-manage-list">
                        <div class="sm-manage-row" data-id="m1">
                            <div class="sm-manage-cell material-info">
                                <div class="sm-item-icon">PDF</div>
                                <h4 class="sm-item-title">Intro to Machine Learning</h4>
                            </div>
                            <div class="sm-manage-cell">Document</div>
                            <div class="sm-manage-cell">AI/ML</div>
                            <div class="sm-manage-cell">Sep 22, 2025</div>
                            <div class="sm-manage-cell actions">
                                <button class="icon-btn edit-btn" data-edit="m1" aria-label="Edit"><i data-lucide="edit-2"></i></button>
                                <button class="icon-btn delete-btn" data-delete="m1" aria-label="Delete"><i data-lucide="trash-2"></i></button>
                            </div>
                        </div>
                        <div class="sm-manage-row" data-id="m2">
                            <div class="sm-manage-cell material-info">
                                <div class="sm-item-icon">VID</div>
                                <h4 class="sm-item-title">Linear Regression Deep Dive</h4>
                            </div>
                            <div class="sm-manage-cell">Video</div>
                            <div class="sm-manage-cell">AI/ML</div>
                            <div class="sm-manage-cell">Sep 12, 2025</div>
                            <div class="sm-manage-cell actions">
                                <button class="icon-btn edit-btn" data-edit="m2" aria-label="Edit"><i data-lucide="edit-2"></i></button>
                                <button class="icon-btn delete-btn" data-delete="m2" aria-label="Delete"><i data-lucide="trash-2"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="sm-manage-empty" id="smManageEmptyMessage">
                        <p>No study materials to manage yet.</p>
                    </div>
    
                </div>
           </div>
        </div>
    </div>
</div>
