(function() {
const smFormModal = document.getElementById('formStudyMaterialModal');
const smFormClose = document.getElementById('closeFromModalBtn');
const categorySuggestionContainer = document.getElementById('categorySuggestion');

const hiddenCategoryInput = document.getElementById('sm-category-confirmed');
const displayedCategoryInput = document.getElementById('sm-category');

const smUploadForm = document.getElementById('sm-upload-form');
const smTypeSelect = document.getElementById('sm-type');
const smFileField = document.getElementById('sm-file-field');
const smLinkField = document.getElementById('sm-link-field');
const smFileInput = document.getElementById('sm-file');
const smLinkInput = document.getElementById('sm-link');

// Close modal handlers
smFormClose.addEventListener('click', () => {
    resetStudyMaterialModal();
});

// Form submission
smUploadForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(smUploadForm);

    var url = '';
    if (smUploadForm.getAttribute('type') === 'update') {
        formData.append('id', smUploadForm.getAttribute('data-id'));
        url = '/studyMaterial/updateSM';
    } else if (smUploadForm.getAttribute('type') === 'create') {
        url = '/studyMaterial/createNewSM';
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

// Handle file link toggle 
smTypeSelect.addEventListener('change', () => {
    const type = smTypeSelect.value;
    
    if (type === 'link') {
        smFileField.classList.add('hidden');
        smLinkField.classList.remove('hidden');
        smFileInput.removeAttribute('required');
        smFileInput.value = ''; 
        smLinkInput.setAttribute('required', 'required');
    } else {
        smFileField.classList.remove('hidden');
        smLinkField.classList.add('hidden');
        smFileInput.setAttribute('required', 'required');
        smLinkInput.removeAttribute('required');
        smLinkInput.value = ''; 
        
        if(type === 'video'){
            smFileInput.setAttribute('accept', '.mp4,.mov');
        } else {
            smFileInput.setAttribute('accept', '.pdf,.ppt,.pptx,.doc,.docx');
        }
    }
});

categorySuggestionContainer.addEventListener('sm:category-suggestion-click', (e) => {

    const clickedElement = e.detail.element;
    const categoryId = clickedElement.getAttribute('data-id');
    const categoryText = clickedElement.textContent;

    hiddenCategoryInput.value = categoryId;
    hiddenCategoryInput.setAttribute('data-text', categoryText);
    displayedCategoryInput.value = categoryText;

    smFormCategoryScroll.abort();
    smFormCategoryScroll.resetScroll();

});

var categorySuggestionTimeout;
function filterAndRender() {
    const debounceDelay = 300;
    if(categorySuggestionTimeout){
        clearTimeout(categorySuggestionTimeout);
    }

    smFormCategoryScroll.abort();
    smFormCategoryScroll.resetScroll();
 
    if(displayedCategoryInput.value === ''){return;}

    categorySuggestionTimeout = setTimeout(async () => {
        smFormCategoryScroll.loadNextElements();
    }, debounceDelay);
}

displayedCategoryInput.addEventListener('focus', filterAndRender);
displayedCategoryInput.addEventListener('input', filterAndRender);

// This race condition is handled strictly by mouse down event disabling default behavior
displayedCategoryInput.addEventListener('blur', () => {
    fallbackCategorySuggestion();
});
displayedCategoryInput.addEventListener('sm:category-refresh', () => {
    fallbackCategorySuggestion();
});


function fallbackCategorySuggestion() {
    smFormCategoryScroll.abort();
    smFormCategoryScroll.resetScroll();
    
    // Revert back to last confirmed category on blur
    if (hiddenCategoryInput.value) {
        displayedCategoryInput.value = hiddenCategoryInput.getAttribute('data-text') || '';
    } else {
        displayedCategoryInput.value = '';
    }
}

smFormCategoryScroll.setupAutoLoadOnScroll();
smFormCategoryScroll.setContextProvider(() => {
    return {
        query: displayedCategoryInput.value
    };
});

function resetStudyMaterialModal() {
    smUploadForm.reset();

    // Reset form type
    smUploadForm.removeAttribute('type');
    smUploadForm.removeAttribute('data-id');

    // Reset to default type (document)
    smTypeSelect.value = 'document';
    smTypeSelect.dispatchEvent(new Event('change')); 

    // Close modal
    smFormModal.classList.remove('active');
}
})();