// My Kuppis modal
(function(){
  let currentTabId = 'myHosts';
  let listenersBound = false;

  /* ── helpers ────────────────────────────────────── */

  /** Show only the sub-filter row that belongs to the active tab */
  function showSubFilters(body, tabId) {
    body.querySelectorAll('.kuppi-modal-filters').forEach(row => {
      if (row.dataset.tab === tabId) {
        row.classList.add('visible');
        // auto-select "All" when switching tabs (if an All button exists)
        const allBtn = row.querySelector('button[data-status-filter=""]');
        if (allBtn && !row.querySelector('button.active')) {
          row.querySelectorAll('button').forEach(b => b.classList.remove('active'));
          allBtn.classList.add('active');
        }
      } else {
        row.classList.remove('visible');
      }
    });
  }

  /** Currently selected sub-filter status for My Attends */
  let attendsStatusFilter = '';
  let hostsStatusFilter = ''; 
  let requestsStatusFilter = '';

  /** Client-side filter: show / hide cards in #my-kuppi-content */
  function setReviewedLayout(enabled) {
    const container = document.getElementById('my-kuppi-content');
    if (!container) return;
    container.classList.toggle('reviewed-table-list', !!enabled);
  }

  function applyStatusFilter(selectedStatus) {
    const container = document.getElementById('my-kuppi-content');
    if (!container) return;
    container.querySelectorAll('.kuppi-post').forEach(card => {
      const ribbon = (card.querySelector('.kuppi-ribbon')?.textContent || '').trim();
      card.style.display = (!selectedStatus || ribbon === selectedStatus) ? '' : 'none';
    });
  }
  function fetchHostsWithStatus(status) {
    setReviewedLayout(false);
    hostsStatusFilter = status ;
    const scroll = window.newMyHostKuppiScroll;
    if(!scroll) return;
    scroll.resetScroll();
    scroll.loadNextElements();
  }

  function fetchRequestsWithStatus(status) {
    setReviewedLayout(false);
    requestsStatusFilter = status;
    const scroll = window.newMyRequestsScroll;
    if(!scroll) return;
    scroll.resetScroll();
    scroll.loadNextElements();
  }
  function fetchAttendsWithStatus(status) {
    if(status != 'Reviewed') {
      setReviewedLayout(false);
      attendsStatusFilter = status;
      const scroll = window.newMyParticipationsScroll;
      if (!scroll) return;
      scroll.resetScroll();
      scroll.loadNextElements();

    } else {
      setReviewedLayout(true);
      const scroll = window.newReviewedKuppiScroll;
      if (!scroll) return;
      scroll.resetScroll();
      scroll.loadNextElements();

    }
  } 
  function fetchFavoriteKuppiCategories() {
    setReviewedLayout(false);
    const scroll = window.newFavoriteCategoriesScroll;
    if (!scroll) return;
    scroll.resetScroll();
    scroll.loadNextElements();
    
  }

  function renderFavoriteCategories(item) {
    const list = document.getElementById('my-kuppi-content');
    
    // Add header if this is the first item being rendered
    if (list && list.children.length === 0) {
      const header = document.createElement('div');
      header.className = 'fav-categories-header';
      header.innerHTML = `
        <div class="fav-col">Category Name</div>
        <div class="fav-col text-right">Actions</div>
      `;
      list.appendChild(header);
    }

    const row = document.createElement('div');
    row.className = 'fav-category-row';

    const nameCol = document.createElement('div');
    nameCol.className = 'fav-col fav-name';
    nameCol.textContent = item.category_name ;
    row.appendChild(nameCol);

    const actionCol = document.createElement('div');
    actionCol.className = 'fav-col fav-actions text-right';
    
    const removeBtn = document.createElement('button');
    removeBtn.className = 'icon-btn btn-danger-outline';
    removeBtn.title = 'Remove Category';
    removeBtn.innerHTML = `<i data-lucide="trash-2"></i>`;
    
    if (window.lucide) {
      setTimeout(() => window.lucide.createIcons({ root: removeBtn }), 0);
    }
    
    removeBtn.onclick = function() {
      if(confirm('Are you sure you want to remove this category from your favorites?')) {
         const data = {
             action: 'toggleFavourite',
             kuppiId: item.id || null, // Assuming the backend needs kuppiId or category_id
             currentStatus: true,
             categoryId: item.category_id || item.id
         };

         Ajax.jsonPost('/kuppi/removeFavoriteCategory', { categoryId: item.category_id || item.id }).then(res => {
             if(res && res.success){
                 row.remove();
             } else {
                 alert('Error removing category');
             }
         }).catch(err => {
             alert('Error removing category from server');
         });
      }
    };
    
    actionCol.appendChild(removeBtn);
    row.appendChild(actionCol);

    return row;
  }
  window.renderFavoriteCategories = renderFavoriteCategories;

  /* ── main open / close ─────────────────────────── */

  function openMyKuppisModal() {
    const overlay = document.getElementById('myKuppisModal');
    const body    = document.getElementById('myKuppisModalBody');
    if (!overlay || !body) return;

    const tabs = body.querySelector('.kuppi-modal-tabs');
    if (!tabs) return;

    const setActiveTab = (btn) => {
      tabs.querySelectorAll('button').forEach(b => b.classList.remove('active'));
      if (btn) {
        btn.classList.add('active');
        if (btn.id) currentTabId = btn.id;
      }
      showSubFilters(body, currentTabId);
    };

    // Centralized loaders per tab
    const loaders = {
      myHosts: () => {
        setReviewedLayout(false);
        window.newMyHostKuppiScroll?.resetScroll?.();
        window.newMyHostKuppiScroll?.loadNextElements?.();
      },
      myRequests: () => {
        setReviewedLayout(false);
        window.newMyRequestsScroll?.resetScroll?.();
        window.newMyRequestsScroll?.loadNextElements?.();
      },
      reports: () => {
        setReviewedLayout(false);
        window.newReportsKuppiScroll?.resetScroll?.();
        window.newReportsKuppiScroll?.loadNextElements?.();
      },
      myAttends: () => {
        const activeBtn = document.querySelector('#my-attends-filters button.active[data-status-filter]');
        const status = activeBtn ? (activeBtn.getAttribute('data-status-filter') || '') : '';
        fetchAttendsWithStatus(status);
      },
      myFavourites: () => {
        setReviewedLayout(false);
        window.newMyFavoritesScroll?.resetScroll?.();
        window.newMyFavoritesScroll?.loadNextElements?.();
      }
    };

    if (!listenersBound) {
      // Tab click handlers
      tabs.querySelectorAll('button').forEach(btn => {
        btn.addEventListener('click', (e) => {
          e.preventDefault();
          setActiveTab(btn);
          loaders[btn.id]?.();
        });
      });

      // Delegated sub-filter click handler for ALL filter rows
      body.querySelectorAll('.kuppi-modal-filters').forEach(filterRow => {
        filterRow.addEventListener('click', (e) => {
          const btn = e.target.closest('button[data-status-filter]');
          if (!btn) return;

          const selectedStatus = btn.getAttribute('data-status-filter') || '';
          filterRow.querySelectorAll('button[data-status-filter]').forEach(b => b.classList.remove('active'));
          btn.classList.add('active');

          const tab = filterRow.dataset.tab;

          switch (tab) {

            case 'myHosts'      : fetchHostsWithStatus(selectedStatus)    ; break;
            case 'myRequests'   : fetchRequestsWithStatus(selectedStatus) ; break;
            case 'reports'      : ;break;
            case 'myAttends'    : fetchAttendsWithStatus(selectedStatus)  ; break;
            case 'myFavourites' : ;break;
            default             : applyStatusFilter(selectedStatus)       ; break;
          }
        });
      });

      // "Manage Topics" button in My Favourites
      const manageTopicsBtn = document.getElementById('manageFavTopicsBtn');
      if (manageTopicsBtn) {
        manageTopicsBtn.addEventListener('click', () => {
          fetchFavoriteKuppiCategories();
          console.log('Manage favourite topics clicked');
        });
      }

      listenersBound = true;
    }

    overlay.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    const currentBtn = tabs.querySelector(`#${currentTabId}`) || tabs.querySelector('#myHosts');
    setActiveTab(currentBtn);
    loaders[currentTabId]?.();
  }

  function closeMyKuppisModal() {
    const overlay = document.getElementById('myKuppisModal');
    if (overlay) overlay.style.display = 'none';
    document.body.style.overflow = '';
  }

  window.openMyKuppisModal  = openMyKuppisModal;
  window.closeMyKuppisModal = closeMyKuppisModal;
})();
