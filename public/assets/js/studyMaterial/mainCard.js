(function() {
    const smGrid = document.getElementById('sm-grid');
    const orderSelectElement = document.getElementById('sm-sort-select');
    const searchInputElement = document.getElementById('sm-search-input');

    const debounceDelay = 300;
    var debounceTimer = null;

    var selectedOrderBy = 'recent';
    var searchTerm = '';

    mainStudyMaterialScroll.setupAutoLoadOnScroll();
    mainStudyMaterialScroll.loadNextElements();

    mainStudyMaterialScroll.setContextProvider(() => {
        return {
            orderBy: selectedOrderBy,
            searchTerm: searchTerm
        };
    });

    orderSelectElement.addEventListener('change', (e) => {
        selectedOrderBy = e.target.value;
        mainStudyMaterialScroll.refresh();
    });

    searchInputElement.addEventListener('input', (e) => {
       
        if(debounceTimer) {
            clearTimeout(debounceTimer);
        }

        debounceTimer = setTimeout(() => {
            searchTerm = e.target.value;
            mainStudyMaterialScroll.refresh();
        }, debounceDelay);
    });

    searchInputElement.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            searchTerm = e.target.value;
            mainStudyMaterialScroll.refresh();
        }
    });

    smGrid.addEventListener('sm:view-click', (e) => {
        const { id } = e.detail;

        window.location.href = `/studymaterial/${id}`;
    });

    smGrid.addEventListener('sm:share-click', async (e) => {
        const { id } = e.detail;
        const url = `${window.location.origin}/studymaterial/${id}`;

        try {
            await navigator.clipboard.writeText(url);
            alert('Link copied to clipboard!');
        } catch (err) {
            console.error('Failed to copy: ', err);
            alert('Failed to copy link');
        }
    });

    mainStudyMaterialScroll.addEventListener('successfulLoad', () => {
        lucide.createIcons();
    });

})();


