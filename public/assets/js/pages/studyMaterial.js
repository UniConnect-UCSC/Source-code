(function(){
  const $ = (sel, ctx=document) => ctx.querySelector(sel);
  const $$ = (sel, ctx=document) => Array.from(ctx.querySelectorAll(sel));

  // Toggle file/link fields by type
  const typeSelect = $('#sm-type');
  const fileField = $('#sm-file-field');
  const linkField = $('#sm-link-field');
  function updateTypeVisibility(){
    const v = typeSelect.value;
    if (v === 'link') {
      fileField.classList.add('hidden');
      linkField.classList.remove('hidden');
    } else {
      linkField.classList.add('hidden');
      fileField.classList.remove('hidden');
    }
  }
  if (typeSelect) {
    typeSelect.addEventListener('change', updateTypeVisibility);
    updateTypeVisibility();
  }

  // Clear form
  const form = $('#sm-upload-form');
  const clearBtn = $('#sm-clear-btn');
  if (clearBtn && form) {
    clearBtn.addEventListener('click', () => {
      form.reset();
      updateTypeVisibility();
    });
  }

  // Basic modal helpers
  function openModal(modal){
    if (!modal) return;
    modal.setAttribute('aria-hidden', 'false');
  }
  function closeModal(modal){
    if (!modal) return;
    modal.setAttribute('aria-hidden', 'true');
  }
  function wireModalClose(modal){
    $$('[data-close-modal]', modal).forEach(btn => btn.addEventListener('click', () => closeModal(modal)));
    $('.sm-modal-backdrop', modal)?.addEventListener('click', () => closeModal(modal));
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closeModal(modal);
    });
  }

  // Edit functionality (UI-only)
  const editModal = $('#sm-edit-modal');
  const editTitleInput = $('#sm-edit-title-input');
  const editDescInput = $('#sm-edit-description');
  const editIdInput = $('#sm-edit-id');
  wireModalClose(editModal);

  let currentEditItem = null;
  $$('#sm-manage-list [data-edit]').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-edit');
      const li = btn.closest('.sm-item');
      currentEditItem = li;
      editIdInput.value = id;
      editTitleInput.value = $('.sm-item-title', li)?.textContent?.trim() || '';
      editDescInput.value = '';
      openModal(editModal);
    });
  });
  $('#sm-edit-save')?.addEventListener('click', () => {
    if (currentEditItem) {
      const title = editTitleInput.value.trim();
      if (title) {
        const titleEl = $('.sm-item-title', currentEditItem);
        if (titleEl) titleEl.textContent = title;
      }
    }
    closeModal(editModal);
  });

  // Delete functionality (UI-only)
  const deleteModal = $('#sm-delete-modal');
  wireModalClose(deleteModal);
  let pendingDeleteItem = null;
  $$('#sm-manage-list [data-delete]').forEach(btn => {
    btn.addEventListener('click', () => {
      pendingDeleteItem = btn.closest('.sm-item');
      openModal(deleteModal);
    });
  });
  $('#sm-delete-confirm')?.addEventListener('click', () => {
    if (pendingDeleteItem) {
      pendingDeleteItem.remove();
    }
    pendingDeleteItem = null;
    closeModal(deleteModal);
  });

  // Wire Add / Manage buttons and modals
  const addModal = $('#sm-add-modal');
  const manageModal = $('#sm-manage-modal');
  wireModalClose(addModal);
  wireModalClose(manageModal);
  $('#sm-add-btn')?.addEventListener('click', () => openModal(addModal));
  $('#sm-edit-manage-btn')?.addEventListener('click', () => openModal(manageModal));
  $('#sm-delete-manage-btn')?.addEventListener('click', () => openModal(manageModal));

  // Search expand/collapse (match events page behavior)
  const sToggle = $('#smSearchToggleBtn');
  const sWrapper = $('#smSearchInputWrapper');
  sToggle?.addEventListener('click', () => {
    sWrapper?.classList.toggle('active');
  });

  // Upload submit (UI-only demo)
  $('#sm-upload-btn')?.addEventListener('click', () => {
    const title = $('#sm-title')?.value?.trim();
    if (!title) return;
    const type = $('#sm-type')?.value || 'document';
    const topic = $('#sm-topic')?.value || 'other';

    // 1) Add to feed grid
    const card = document.createElement('article');
    card.className = 'sm-material-card';
  card.dataset.type = type;
  card.dataset.topic = topic;
    const iconLabel = type === 'video' ? 'VID' : (type === 'link' ? 'URL' : 'PDF');
    const iconClass = type === 'video' ? 'vid' : (type === 'link' ? 'lnk' : 'doc');
    card.innerHTML = `
      <div class="sm-material-icon ${iconClass}">${iconLabel}</div>
      <div class="sm-material-content">
        <h4 class="sm-material-title"></h4>
        <div class="sm-item-meta">Your University • ${type.charAt(0).toUpperCase()+type.slice(1)} • just now</div>
      </div>`;
    $('.sm-material-title', card).textContent = title;
    $('#sm-grid')?.prepend(card);

    // 2) Add to manage list
    const li = document.createElement('li');
    li.className = 'sm-item';
    li.innerHTML = `
      <div class="sm-item-main">
        <div class="sm-item-icon ${iconClass}">${iconLabel}</div>
        <div class="sm-item-info">
          <h4 class="sm-item-title"></h4>
          <div class="sm-item-meta">${type.charAt(0).toUpperCase()+type.slice(1)} • just now</div>
        </div>
      </div>
      <div class="sm-item-actions">
        <button class="btn btn-ghost" data-edit>Edit</button>
        <button class="btn btn-danger" data-delete>Delete</button>
      </div>`;
    $('.sm-item-title', li).textContent = title;
    $('#sm-manage-list')?.prepend(li);

    // Attach handlers to the new manage item
    $('[data-edit]', li)?.addEventListener('click', () => {
      currentEditItem = li;
      editTitleInput.value = title;
      openModal(editModal);
    });
    $('[data-delete]', li)?.addEventListener('click', () => {
      pendingDeleteItem = li;
      openModal(deleteModal);
    });

    // Clear and close
    form?.reset();
    updateTypeVisibility();
    closeModal(addModal);
    applyFilters();
  });

  // Category filtering and search (events-like structure)
  const categories = $$('#categoryWrapper .category-btn');
  function applyFilters(){
    const activeCat = $('#categoryWrapper .category-btn.active')?.getAttribute('data-category') || 'all';
    const q = ($('#sm-search-input')?.value || '').trim().toLowerCase();
    $$('#sm-grid .sm-material-card').forEach(card => {
      const topic = card.dataset.topic || 'other';
      const matchesCat = activeCat === 'all' || topic === activeCat;
      const txt = `${$('.sm-material-title', card)?.textContent || ''} ${(card.textContent || '')}`.toLowerCase();
      const matchesQ = !q || txt.includes(q);
      card.style.display = (matchesCat && matchesQ) ? '' : 'none';
    });
  }
  categories.forEach(btn => btn.addEventListener('click', () => {
    categories.forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    applyFilters();
  }));
  $('#sm-search-input')?.addEventListener('input', applyFilters);
  applyFilters();

  // Sorting
  function sortGrid(by){
    const grid = $('#sm-grid');
    if (!grid) return;
    const cards = Array.from(grid.children);
    let comparator = (a,b) => 0;
    if (by === 'title') {
      comparator = (a,b) => ($('.sm-material-title', a)?.textContent||'').localeCompare(($('.sm-material-title', b)?.textContent||''));
    } else if (by === 'popular') {
      comparator = (a,b) => (parseInt(b.dataset.views||'0') - parseInt(a.dataset.views||'0'));
    } else { // recent (default)
      comparator = (a,b) => new Date(b.dataset.ts||0) - new Date(a.dataset.ts||0);
    }
    cards.sort(comparator).forEach(c => grid.appendChild(c));
  }
  $('#sm-sort-select')?.addEventListener('change', (e)=> sortGrid(e.target.value));
  sortGrid('recent');
})();
