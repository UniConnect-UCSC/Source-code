const smAddBtn = document.getElementById('sm-add-btn');
const smManageBtn = document.getElementById('sm-manage-btn');
const smAddModal = document.getElementById('formStudyMaterialModal');
const smManageModal = document.getElementById('manageStudyMaterialsModal');

// For hiding body scroll when modal is open
document.body.classList.add('body-class');

// Create Study Material button
if (smAddBtn) {
    smAddBtn.addEventListener('click', () => {
        // Update modal header and button text for create mode
        document.getElementById("sm-add-title").textContent = 'Add Study Material';
        
        // Set form to create mode
        const smUploadForm = document.getElementById('sm-upload-form');
        smUploadForm.setAttribute('type', 'create');
        smUploadForm.removeAttribute('data-id');

        document.getElementById("sm-type").disabled = false;
        document.getElementById("sm-file").disabled = false;
        document.getElementById("sm-link").disabled = false;
        
        // Open modal
        smAddModal.classList.add('active');
    });
}

// Manage Study Materials button
if (smManageBtn) {
    smManageBtn.addEventListener('click', async () => {

    if(await myStudyMaterialScroll.refresh()){
        document.getElementById('smManageEmptyMessage').style.display = 'none';
    }else{
        document.getElementById('smManageEmptyMessage').style.display = 'block'; 
    }

    lucide.createIcons();
    
    smManageModal.classList.add('active');
    });
}
