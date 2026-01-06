const formEventModal = document.getElementById('formEventModal');
const closeModalBtn = document.getElementById('closeModalBtn');
const eventForm = document.getElementById('eventForm');

closeModalBtn.addEventListener('click', () => {
    resetEventModal();
});

eventForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(eventForm);

    var url = '';
    if (eventForm.getAttribute('type') === 'update') {
        formData.append('event_id', eventForm.getAttribute('data-id'));
        url = '/event/updateEvent';
    } else if (eventForm.getAttribute('type') === 'create') {
        url = '/event/createNewEvent';
    }

    // Send FormData (do not set Content-Type, browser will handle it)

    console.log('Form data being sent:', ...formData.entries());

    const $response = await Ajax.formDataPost(url, formData);

    if ($response['status'] === 'success') {
        console.log('Event successfully created/updated');
    } else {
        console.error('Error creating event:', $response);
    }

    // Close and fully reset modal state (clear image + empty categories)
    resetEventModal();
});

// Image preview handling
const imgInput = document.getElementById('eventImage');
const previewWrapper = document.getElementById('eventImagePreviewWrapper');
const previewImg = document.getElementById('eventImagePreview');
const clearBtn = document.getElementById('clearEventImageBtn');

function clearImage(){
    if (!imgInput) return;
    imgInput.value = '';
    if (previewWrapper && previewImg) {
        previewImg.removeAttribute('src');
        previewWrapper.style.display = 'none';
    }
}

if (imgInput) {
imgInput.addEventListener('change', () => {
    const file = imgInput.files && imgInput.files[0];
    if (!file) { clearImage(); return; }
    const reader = new FileReader();
    reader.onload = () => {
    if (previewImg && previewWrapper) {
        previewImg.src = reader.result;
        previewWrapper.style.display = 'flex';
    }
    };
    reader.readAsDataURL(file);
});
}

if (clearBtn) clearBtn.addEventListener('click', clearImage);


//Category selector
const categoryInput = document.getElementById('categoryInput');
const selectedElement = document.getElementById('eventCategories');
const suggestionsDiv = document.getElementById('categorySuggestions');
const selectedList = [];
var filterTimeout;

function renderTags(){
    
    const tagContainer = document.getElementById('selectedCategories');
    tagContainer.innerHTML = '';

    selectedList.forEach(selection => {
        const chip = document.createElement('span');
        chip.className = 'tag-chip';
        chip.textContent = selection['name'];
        
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'remove-btn';
        btn.textContent = '×';

        btn.addEventListener('click', () => {
            removeCategory(selection);
        });

        chip.appendChild(document.createTextNode(' '));
        chip.appendChild(btn);
        tagContainer.appendChild(chip);

    });

}

function removeCategory(selection){

    const index = selectedList.findIndex(item => item.id === selection.id);
    if (index > -1) {
        selectedList.splice(index, 1);
    }

    renderTags();
    updateHidden();
}

function updateHidden(){
    selectedElement.value = JSON.stringify(selectedList.map(cat => cat.id));
}

function addCategory(selection){

    selectedList.push(selection);

    renderTags();
    updateHidden();
    categoryInput.value = ''; 
    filterAndRender();
}

function filterAndRender(){
    const debounceDelay = 300;
    const categoryInputValue = categoryInput.value;

    // Clear existing timeout to debounce
    if(filterTimeout){
        clearTimeout(filterTimeout);
    }

    formCategorySuggestionScroll.abort();
    formCategorySuggestionScroll.resetScroll();

    if(!categoryInputValue){return;}

    filterTimeout = setTimeout(() => {
        formCategorySuggestionScroll.loadNextElements();

    }, debounceDelay);

}

categoryInput.addEventListener('focus',filterAndRender);
categoryInput.addEventListener('input',filterAndRender);
categoryInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
        e.preventDefault(); //prevent form submission

        const suggestions = suggestionsDiv.querySelectorAll('div');
        const suggestionCount = suggestions.length; 
        if(suggestionCount == 1){
            suggestions[0].click();
            return;
        } 
    }
});


formCategorySuggestionScroll.setupAutoLoadOnScroll();

formCategorySuggestionScroll.setContextProvider(() => {
    return {
        'searchTerm': categoryInput.value,
        'excludeIds': selectedList.map(cat => cat.id)
    };
});

function resetEventModal(){
    eventForm.reset();
    clearImage();

    selectedList.length = 0;
    renderTags();
    updateHidden();

    // Hide suggestions and clear input
    categoryInput.value = '';
    filterAndRender();

    // Close modal
    formEventModal.classList.remove('active');
}