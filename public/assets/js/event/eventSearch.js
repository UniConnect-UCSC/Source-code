const searchToggleBtn = document.getElementById('searchToggleBtn');
const searchInputWrapper = document.getElementById('searchInputWrapper');
const searchInput = document.getElementById('searchInput');
const searchSuggestions = document.getElementById('searchSuggestions');
const debounceDelay = 300;
let suggestionsFetchTimeout = null;

// Option search and filtering
searchToggleBtn.addEventListener('click', () => {
    searchInputWrapper.classList.toggle('active');
    document.getElementById('filterButtons').classList.toggle('shifted');
    if (searchInputWrapper.classList.contains('active')) {
        searchInput.focus();
    }
});

// Fetch event suggestions
function fetchEventSuggestions(query) {
    // Min characters to fetch suggestions
    if (!query || query.trim().length < 2) {
        hideSuggestions();
        return;
    }

    hideSuggestions();
  
    searchEventScroll.loadNextElements({
        searchTerm: query 
    });
}

// Hide suggestions
function hideSuggestions() {
    searchEventScroll.abort();
    searchEventScroll.resetScroll();
}

// Execute search
function executeSearch(query) {

    hideSuggestions();
    searchInput.value = query;
    window.selectedEventSearchQuery = query; // Store the search query globally for the event grid context
    newEventScroll.refresh();
}

searchInput.addEventListener('input', (e) => {
    searchQuery = e.target.value;
    
    // Clear previous timeout
    if (suggestionsFetchTimeout) {
        clearTimeout(suggestionsFetchTimeout);
    }
    
    // Debounce the suggestions fetch
    suggestionsFetchTimeout = setTimeout(() => {
        fetchEventSuggestions(searchQuery);
    }, debounceDelay);
});

searchInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
        e.preventDefault();
        executeSearch(searchQuery);
    } else if (e.key === 'Escape') {
        hideSuggestions();
    }
});

// Hide suggestions when clicking outside
document.addEventListener('click', (e) => {
    if (!searchInputWrapper.contains(e.target)) {
        hideSuggestions();
    }
});