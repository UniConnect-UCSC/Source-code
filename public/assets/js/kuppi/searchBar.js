(function () {
  const searchInput = document.getElementById('searchInput');
  const searchBtn = document.getElementById('searchToggleBtn');

  if (!searchInput || !window.newMainKuppiScroll) return;

  const debounceDelay = 300;
  let debounceTimer = null;
  let searchTerm = '';

  // Ensure context provider includes BOTH categories and search term
  window.newMainKuppiScroll.setContextProvider(() => {
    return {
      categories: Array.isArray(window.selectedKuppiFilterCategories)
        ? window.selectedKuppiFilterCategories
        : [],
      searchTerm: searchTerm
    };
  });

  searchInput.addEventListener('input', (e) => {
    if (debounceTimer) clearTimeout(debounceTimer);

    debounceTimer = setTimeout(() => {
      searchTerm = e.target.value.trim();
      window.newMainKuppiScroll.refresh();
    }, debounceDelay);
  });

  searchInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
      e.preventDefault();
      searchTerm = e.target.value.trim();
      window.newMainKuppiScroll.refresh();
    }
  });

  if (searchBtn) {
    searchBtn.addEventListener('click', () => {
      searchTerm = searchInput.value.trim();
      window.newMainKuppiScroll.refresh();
    });
  }
})();