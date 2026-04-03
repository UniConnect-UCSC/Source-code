const categoryWrapper = document.getElementById('categoryWrapper');

categoryScroll.setupAutoLoadOnScroll();
categoryScroll.loadNextElements();

let selectedCategory = [];

categoryWrapper.addEventListener('click', (e) => {
    const btn = e.target.closest('.category-btn');
    const allBtn = document.getElementById('allCategoriesBtn');
    if (!btn) return;
    
    if(btn.id === 'allCategoriesBtn'){
        categoryWrapper.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        selectedCategory = [];
    }else{
        allBtn.classList.remove('active');
        
        if(btn.classList.contains('active')){
            btn.classList.remove('active');
            selectedCategory = selectedCategory.filter(cat => cat !== btn.dataset.categoryId);        
            if(selectedCategory.length === 0){allBtn.classList.add('active');}
        }else{
            btn.classList.add('active');
            selectedCategory.push(btn.dataset.categoryId);
        }
    }

    window.selectedEventFilterCategories = selectedCategory;
    newEventScroll.refresh();
});

