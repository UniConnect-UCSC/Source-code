const manageStudyMaterialsModal = document.getElementById('manageStudyMaterialsModal');
const closeManageBtn = document.getElementById('manageStudyMaterialsModal')?.querySelector('[data-close-modal]');
const smManageList = document.getElementById('sm-manage-list');

if (closeManageBtn) {
    closeManageBtn.addEventListener('click', () => {
        // Use the scroll to reset scroll.reset();
        manageStudyMaterialsModal.classList.remove('active');
    });
}

// Edit and delete button handlers
if (smManageList) {
    smManageList.addEventListener('click', function(e) {
        const editBtn = e.target.closest('.edit-btn');
        const deleteBtn = e.target.closest('.delete-btn');

        if (editBtn) {
            const row = editBtn.closest('.sm-manage-row');
            const materialId = row.getAttribute('data-id');

            // Fetch all data for this study material
            Ajax.jsonPost('/studyMaterial/getStudyMaterialData', { study_material_id: materialId }).then(
                (response) => {
                    if (response.success) {
                        // Populate form fields with material data
                        document.getElementById('sm-title').value = response.data.title || '';
                        document.getElementById('sm-subject').value = response.data.subject || '';
                        document.getElementById('sm-topic').value = response.data.topic || '';
                        document.getElementById('sm-type').value = response.data.type || 'document';
                        document.getElementById('sm-description').value = response.data.description || '';
                        document.getElementById('sm-tags').value = response.data.tags || '';

                        // Handle file/link based on type
                        const type = response.data.type || 'document';
                        const smFileField = document.getElementById('sm-file-field');
                        const smLinkField = document.getElementById('sm-link-field');
                        const smFileInput = document.getElementById('sm-file');
                        const smLinkInput = document.getElementById('sm-link');

                        if (type === 'link') {
                            smFileField.classList.add('hidden');
                            smLinkField.classList.remove('hidden');
                            smFileInput.removeAttribute('required');
                            smLinkInput.setAttribute('required', 'required');
                            smLinkInput.value = response.data.file_link || '';
                        } else {
                            smFileField.classList.remove('hidden');
                            smLinkField.classList.add('hidden');
                            smFileInput.setAttribute('required', 'required');
                            smLinkInput.removeAttribute('required');
                            smFileInput.value = ''; // Reset file input (can't preset file)
                        }

                        // Set form to update mode
                        const smUploadForm = document.getElementById('sm-upload-form');
                        smUploadForm.setAttribute('type', 'update');
                        smUploadForm.setAttribute('data-id', materialId);

                        // Update modal header and button text
                        document.querySelector('#formStudyMaterialModal .sm-modal-header h3').innerText = 'Edit Study Material';
                        document.getElementById('sm-upload-btn').innerText = 'Save Changes';

                        // Close manage modal and open form modal
                        manageStudyMaterialsModal.classList.remove('active');
                        document.getElementById('formStudyMaterialModal').classList.add('active');
                    } else {
                        console.error('Failed to fetch study material data:', response.message);
                    }
                }
            ).catch(err => {
                console.error('Error fetching study material:', err);
            });

        } else if (deleteBtn) {
            const row = deleteBtn.closest('.sm-manage-row');
            const materialId = row.getAttribute('data-id');

            if (confirm('Are you sure you want to delete this study material?')) {
                Ajax.jsonPost('/studyMaterial/deleteStudyMaterial', { study_material_id: materialId })
                    .then(response => {
                        if (response.success) {
                            console.log('Study material deleted successfully');
                            // Refresh the list
                            location.reload(); // Or implement a better refresh mechanism
                        } else {
                            console.error('Failed to delete study material:', response.message);
                        }
                    })
                    .catch(err => {
                        console.error('Error deleting study material:', err);
                    });
            }
        }
    });
}
