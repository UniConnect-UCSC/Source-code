const createEventBtn = document.getElementById('createEventBtn');
const viewEventBtn = document.getElementById('viewEventBtn');
const searchToggleBtn = document.getElementById('searchToggleBtn');
const searchInputWrapper = document.getElementById('searchInputWrapper');
const searchInput = document.getElementById('searchInput');
const filterButtons = document.querySelectorAll('#filterButtons .btn');
const categoryWrapper = document.getElementById('categoryWrapper');

// For hiding body scroll when modal is open
document.body.classList.add('body-class');

// Modal controls
createEventBtn.addEventListener('click', () => {
    document.getElementById('modalHeaderName').innerText = "Create New Event";
    document.getElementById('submitEventBtn').innerText = "Create Event";
    document.getElementById('eventForm').setAttribute('type', 'create');
    formEventModal.classList.add('active');
});

viewEventBtn.addEventListener('click', () => {
    (async () => {
        
        if(await repEventScroll.loadNextElements()){
            lucide.createIcons();
            document.getElementById('repEventsEmptyMessage').style.display = 'none';
        }else{
            document.getElementById('repEventsEmptyMessage').style.display = 'block'; 
        }

        lucide.createIcons();
    })();

    viewEventModal.classList.add('active');
});

// Option search and filtering
searchToggleBtn.addEventListener('click', () => {
    searchInputWrapper.classList.toggle('active');
    document.getElementById('filterButtons').classList.toggle('shifted');
    if (searchInputWrapper.classList.contains('active')) {
        searchInput.focus();
    }
});

searchInput.addEventListener('input', (e) => {
    searchQuery = e.target.value;
    // Implement search filtering logic here (AJAX)
});

filterButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        filterButtons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentFilter = btn.dataset.filter;
    
        // Implement filter logic here

    });
});

selectedCategory = [];

categoryWrapper.addEventListener('click', (e) => {
    const btn = e.target.closest('.category-btn');
    if (!btn) return;
    
    if(btn.id === 'allCategoriesBtn'){
        categoryWrapper.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        selectedCategory = [];
    }else{
        document.getElementById('allCategoriesBtn').classList.remove('active');
        
        if(btn.classList.contains('active')){
            btn.classList.remove('active');
            selectedCategory = selectedCategory.filter(cat => cat !== btn.dataset.categoryId);        
        }else{
            btn.classList.add('active');
            selectedCategory.push(btn.dataset.categoryId);
        }
    }

    //Apply filtering
    newEventScroll.resetScroll();
    newEventScroll.loadNextElements({
        context: { categories: selectedCategory }
    });

});


