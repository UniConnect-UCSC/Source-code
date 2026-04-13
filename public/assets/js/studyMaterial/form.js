const smFormModal = document.getElementById('formStudyMaterialModal');
const smUploadForm = document.getElementById('sm-upload-form');
const smClearBtn = document.getElementById('sm-clear-btn');
const smTypeSelect = document.getElementById('sm-type');
const smFileField = document.getElementById('sm-file-field');
const smLinkField = document.getElementById('sm-link-field');
const smFileInput = document.getElementById('sm-file');
const smLinkInput = document.getElementById('sm-link');

// Close modal handlers
document.querySelectorAll('[data-close-modal]').forEach(btn => {
    btn.addEventListener('click', () => {
        resetStudyMaterialModal();
    });
});

// Form submission
smUploadForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(smUploadForm);

    var url = '';
    if (smUploadForm.getAttribute('type') === 'update') {
        formData.append('study_material_id', smUploadForm.getAttribute('data-id'));
        url = '/studyMaterial/updateStudyMaterial';
    } else if (smUploadForm.getAttribute('type') === 'create') {
        url = '/studyMaterial/createNewStudyMaterial';
    }

    console.log('Study Material form data being sent:', ...formData.entries());

    const $response = await Ajax.formDataPost(url, formData);

    if ($response['status'] === 'success') {
        console.log('Study material successfully created/updated');
    } else {
        console.error('Error creating/updating study material:', $response);
    }

    // Close and fully reset modal state
    resetStudyMaterialModal();
});

// Handle file/link toggle based on type selection
smTypeSelect.addEventListener('change', () => {
    const type = smTypeSelect.value;
    
    if (type === 'link') {
        smFileField.classList.add('hidden');
        smLinkField.classList.remove('hidden');
        smFileInput.removeAttribute('required');
        smLinkInput.setAttribute('required', 'required');
    } else {
        smFileField.classList.remove('hidden');
        smLinkField.classList.add('hidden');
        smFileInput.setAttribute('required', 'required');
        smLinkInput.removeAttribute('required');
    }
});

// Clear form button
smClearBtn.addEventListener('click', () => {
    smUploadForm.reset();
    clearStudyMaterialImage();
});

// Image preview handling
const smFileImageInput = document.getElementById('sm-file'); // Make sure this is the file input, not link
// For study materials, assuming there might be thumbnail upload, adjust as needed

// Additional helper function if needed for thumbnails
function clearStudyMaterialImage() {
    // Placeholder for future image preview clearing if implemented
    // For now, study materials don't have image preview like events
}

function resetStudyMaterialModal() {
    smUploadForm.reset();
    clearStudyMaterialImage();

    // Reset form type
    smUploadForm.removeAttribute('type');
    smUploadForm.removeAttribute('data-id');

    // Reset to default type (document)
    smTypeSelect.value = 'document';
    smFileField.classList.remove('hidden');
    smLinkField.classList.add('hidden');
    smFileInput.setAttribute('required', 'required');
    smLinkInput.removeAttribute('required');

    // Close modal
    smFormModal.classList.remove('active');
}
