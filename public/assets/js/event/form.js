const formEventModal = document.getElementById('formEventModal');
const closeModalBtn = document.getElementById('closeModalBtn');
const eventForm = document.getElementById('eventForm');

closeModalBtn.addEventListener('click', () => {
    formEventModal.classList.remove('active');
    eventForm.reset();
});

/*
formEventModal.addEventListener('click', (e) => {
    if (e.target === formEventModal) {
        formEventModal.classList.remove('active');
        eventForm.reset(); 
    }
});
*/

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

    eventForm.reset();
    formEventModal.classList.remove('active');
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
const suggestionsWrapper = document.getElementById('categorySuggestionsWrapper');
const loadingIndicator = document.getElementById('categoryLoadingIndicator');
const selectedList = JSON.parse(selectedElement.value);
var noMoreSuggestions = false;
var isLoading = false;

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
    selectedElement.value = JSON.stringify(selectedList);
}

function addCategory(selection){

    selectedList.push(selection);

    renderTags();
    updateHidden();
    suggestionsWrapper.classList.remove('show');
    categoryInput.value = ''; 
}

function filterAndRender(){

    const categoryInputValue = categoryInput.value;

    if(!categoryInputValue){
        suggestionsWrapper.classList.remove('show');
        return;
    }

    formCategorySuggestionScroll.resetScroll();
    noMoreSuggestions = false;
    formCategorySuggestionScroll.loadNextElements({
        'searchTerm': categoryInputValue,
        'excludeIds': selectedList.map(cat => cat.id)
    }).then(() => {
        suggestionsWrapper.classList.add('show');
    });

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

suggestionsWrapper.addEventListener('scroll', () => {
    const { scrollTop, scrollHeight, clientHeight } = suggestionsWrapper;
    const threshold = 20; //px

    if (scrollTop + clientHeight >= scrollHeight - threshold && !noMoreSuggestions && !isLoading) {
        
        isLoading = true;

        loadingIndicator.style.display = 'flex';

        console.log( "loading indicator" + loadingIndicator.style.display);

        formCategorySuggestionScroll.loadNextElements({
            'searchTerm': categoryInput.value,
            'excludeIds': selectedList.map(cat => cat.id)
        }).then( result => {
            if(!result){
                console.log('No more suggestions to load');
                noMoreSuggestions = true;
            }
            loadingIndicator.style.display = 'none';
            console.log( "loading indicator" + loadingIndicator.style.display);
            isLoading = false;
        });
    }
});