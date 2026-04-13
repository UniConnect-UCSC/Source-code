(function () {
  function capitalize(name) {
    var text = String(name || '');
    if (!text) return '';
    return text.charAt(0).toUpperCase() + text.slice(1);
  }

  function renderKuppiCategories(data) {
    var id = data.id ;
    var rawName = data.category_name ;
    var name = capitalize(rawName);

    var btn = document.createElement('button');
    btn.className = 'category-btn';
    btn.setAttribute('data-category-id', id);
    btn.textContent = name;

    return btn;
  }

  var categoryWrapper = document.getElementById('categoryWrapper');
  var selectedCategory = [];

  if (categoryWrapper) {
    categoryWrapper.addEventListener('click', function (e) {
      var btn = e.target.closest('.category-btn');
      var allBtn = document.getElementById('allCategoriesBtn');
      if (!btn || !allBtn) return;

      if (btn.id === 'allCategoriesBtn') {
        categoryWrapper.querySelectorAll('.category-btn').forEach(function (b) {
          b.classList.remove('active');
        });
        btn.classList.add('active');
        selectedCategory = [];
      } else {
        allBtn.classList.remove('active');

        if (btn.classList.contains('active')) {
          btn.classList.remove('active');
          selectedCategory = selectedCategory.filter(function (catId) {
            return String(catId) !== String(btn.dataset.categoryId);
          });
          if (selectedCategory.length === 0) allBtn.classList.add('active');
        } else {
          btn.classList.add('active');
          selectedCategory.push(btn.dataset.categoryId);
        }
      }

      window.selectedKuppiFilterCategories = selectedCategory;
      if (window.newMainKuppiScroll && typeof window.newMainKuppiScroll.refresh === 'function') {
        window.newMainKuppiScroll.refresh();
      }
    });
  }

  window.selectedKuppiFilterCategories = [];
  window.renderKuppiCategories = renderKuppiCategories;
})();