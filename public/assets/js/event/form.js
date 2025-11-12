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
const selectedList = JSON.parse(selectedElement.value);


const dummyCategories = [
    {id: 1, name: 'Music'},
    {id: 2, name: 'Art'},
    {id: 3, name: 'Sports'},
    {id: 4, name: 'Technology'},
    {id: 5, name: 'Education'},
    {id: 6, name: 'Health'},
    {id: 7, name: 'Business'},
    {id: 8, name: 'Travel'},
    {id: 9, name: 'Food'},
    {id: 10, name: 'Gaming'},
];



//Return would be [{id=>5, name=>music},{},{}]
function findSuggestions(value){

    const valLower = value.toLowerCase();
    const filtered = dummyCategories.filter(cat => 
        cat.name.toLowerCase().includes(valLower) &&
        !selectedList.some(sel => sel.id === cat.id)
    );

    return filtered;
}

function renderSuggestions(suggestions){

    suggestionsDiv.innerHTML = '';
    suggestions.forEach(suggestion => {
        const div = categorySuggestionRenderer(suggestion);
        suggestionsDiv.appendChild(div);
    });

    suggestionsDiv.classList.add('show');
}

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
    suggestionsDiv.classList.remove('show');
    categoryInput.value = ''; 
}

function filterAndRender(){

    const categoryInputValue = categoryInput.value;

    if(!categoryInputValue){
        suggestionsDiv.classList.remove('show');
        return;
    }

    formCategorySuggestionScroll.resetScroll();
    formCategorySuggestionScroll.loadNextElements({
        'searchTerm': categoryInputValue,
        'excludeIds': selectedList.map(cat => cat.id)
    }).then(() => {
        suggestionsDiv.classList.add('show');
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