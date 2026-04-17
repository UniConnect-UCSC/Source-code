const manageStudyMaterialsModal = document.getElementById('manageStudyMaterialsModal');
const closeManageBtn = document.getElementById('closeStudyMaterialModalBtn');
const smManageList = document.getElementById('sm-manage-list');

if (closeManageBtn) {
    closeManageBtn.addEventListener('click', () => {
        manageStudyMaterialsModal.classList.remove('active');
    });
}

myStudyMaterialScroll.setupAutoLoadOnScroll();

myStudyMaterialScroll.addEventListener('successfulLoad', () => {
    lucide.createIcons(); 
});



// Edit and delete button handlers
if (smManageList) {
    smManageList.addEventListener('click', function(e) {
        const editBtn = e.target.closest('.edit-btn');
        const deleteBtn = e.target.closest('.delete-btn');

        if (editBtn) {
            const row = editBtn.closest('.sm-manage-row');
            const id = row.getAttribute('data-id');
            const title = row.getAttribute('data-title');
            const category = row.getAttribute('data-category');
            const categoryId = row.getAttribute('data-category-id');
            const description = row.getAttribute('data-description');

            // Populate form fields with material data
            document.getElementById('sm-title').value = title;
            document.getElementById('sm-description').value = description;

            const categoryHidden = document.getElementById('sm-category-confirmed');
            categoryHidden.value = categoryId;
            categoryHidden.setAttribute('data-text', category);
            document.getElementById('sm-category').dispatchEvent(new CustomEvent('sm:category-refresh')); 

            //Disabled editing the file
            document.getElementById('sm-type').disabled = true;
            document.getElementById('sm-file').disabled = true;
            document.getElementById('sm-link').disabled = true;

            // Set form to update mode
            const smUploadForm = document.getElementById('sm-upload-form');
            smUploadForm.setAttribute('type', 'update');
            smUploadForm.setAttribute('data-id', id);

            // Update modal header and button text
            document.getElementById("sm-add-title").innerText = 'Edit Study Material';

            // Close manage modal and open form modal
            manageStudyMaterialsModal.classList.remove('active');
            document.getElementById('formStudyMaterialModal').classList.add('active');

        } else if (deleteBtn) {

            const row = deleteBtn.closest('.sm-manage-row');
            const id = row.getAttribute('data-id');

            if (confirm('Are you sure you want to delete this study material?')) {
                Ajax.jsonPost('/studyMaterial/deleteSM', { id: id });
                manageStudyMaterialsModal.classList.remove('active');
            }
        }
    });
}
