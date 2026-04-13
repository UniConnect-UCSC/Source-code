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
        
        // Open modal
        smAddModal.classList.add('active');
    });
}

// Manage Study Materials button
if (smManageBtn) {
    smManageBtn.addEventListener('click', async () => {

    //if(await myStudyMaterialScroll.loadNextElements()){
    if(true){
        document.getElementById('smManageEmptyMessage').style.display = 'none';
    }else{
        document.getElementById('smManageEmptyMessage').style.display = 'block'; 
    }

    lucide.createIcons();
    
    smManageModal.classList.add('active');

    //     const manageStudyMaterialsModal = document.getElementById('manageStudyMaterialsModal');
        
    //     // Load user's study materials
    //     const response = await Ajax.jsonPost('/studyMaterial/getUserStudyMaterials', {});
        
    //     if (response.success && response.materials) {
    //         const smManageList = document.getElementById('sm-manage-list');
    //         smManageList.innerHTML = ''; // Clear list

    //         response.materials.forEach(material => {
    //             const row = document.createElement('div');
    //             row.className = 'sm-manage-row';
    //             row.setAttribute('data-id', material.id);
                
    //             const typeLabel = material.type === 'document' ? 'Document' : 
    //                              material.type === 'video' ? 'Video' : 'Link';
                
    //             const iconClass = material.type === 'document' ? 'doc' :
    //                              material.type === 'video' ? 'vid' : 'lnk';
                
    //             const iconText = material.type === 'document' ? 'PDF' :
    //                             material.type === 'video' ? 'VID' : 'URL';

    //             row.innerHTML = `
    //                 <div class="sm-manage-cell material-info">
    //                     <div class="sm-item-icon ${iconClass}">${iconText}</div>
    //                     <h4 class="sm-item-title">${material.title}</h4>
    //                 </div>
    //                 <div class="sm-manage-cell">${typeLabel}</div>
    //                 <div class="sm-manage-cell">${material.topic || 'Other'}</div>
    //                 <div class="sm-manage-cell">${material.uploaded_date}</div>
    //                 <div class="sm-manage-cell actions">
    //                     <button class="icon-btn edit-btn" aria-label="Edit"><i data-lucide="edit-2"></i></button>
    //                     <button class="icon-btn delete-btn" aria-label="Delete"><i data-lucide="trash-2"></i></button>
    //                 </div>
    //             `;
                
    //             smManageList.appendChild(row);
    //         });

    //         lucide.createIcons();
            
    //         // Hide empty message if materials exist
    //         const emptyMessage = document.getElementById('smManageEmptyMessage');
    //         if (emptyMessage) {
    //             emptyMessage.style.display = response.materials.length === 0 ? 'block' : 'none';
    //         }
    //     } else {
    //         console.error('Failed to load study materials:', response.message);
    //         const emptyMessage = document.getElementById('smManageEmptyMessage');
    //         if (emptyMessage) {
    //             emptyMessage.style.display = 'block';
    //         }
    //     }

    //     // Open manage modal
    //     manageStudyMaterialsModal.classList.add('active');
    });
}
