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
  function applyStatusFilter(selectedStatus) {
    const container = document.getElementById('my-kuppi-content');
    if (!container) return;
    container.querySelectorAll('.kuppi-post').forEach(card => {
      const ribbon = (card.querySelector('.kuppi-ribbon')?.textContent || '').trim();
      card.style.display = (!selectedStatus || ribbon === selectedStatus) ? '' : 'none';
    });
  }
  function fetchHostsWithStatus(status) {
    hostsStatusFilter = status ;
    const scroll = window.newMyHostKuppiScroll;
    if(!scroll) return;
    scroll.resetScroll();
    scroll.loadNextElements();
  }

  function fetchRequestsWithStatus(status) {
    requestsStatusFilter = status;
    const scroll = window.newMyRequestsScroll;
    if(!scroll) return;
    scroll.resetScroll();
    scroll.loadNextElements();
  }
  /** Re-fetch My Attends from backend with the selected status filter */
  function fetchAttendsWithStatus(status) {
    attendsStatusFilter = status;
    const scroll = window.newMyParticipationsScroll;
    if (!scroll) return;
    scroll.resetScroll();
    scroll.loadNextElements();
  }

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
      myHosts:      () => { window.newMyHostKuppiScroll?.resetScroll?.();      window.newMyHostKuppiScroll?.loadNextElements?.(); },
      myRequests:   () => { window.newMyRequestsScroll?.resetScroll?.();      window.newMyRequestsScroll?.loadNextElements?.(); },
      reports:      () => { window.newReportsKuppiScroll?.resetScroll?.();    window.newReportsKuppiScroll?.loadNextElements?.(); },
      myAttends:    () => { window.newMyParticipationsScroll?.resetScroll?.();  window.newMyParticipationsScroll?.loadNextElements?.(); },
      myFavourites: () => { window.newMyFavoritesScroll?.resetScroll?.(); window.newMyFavoritesScroll?.loadNextElements?.(); }
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
          // TODO: open your "manage favourite topics" UI here
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
