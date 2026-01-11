// My Kuppis modal
(function(){
  // Persist last-selected tab across modal opens
  let currentTabId = 'myHosts';
  let listenersBound = false;
  function openMyKuppisModal(){
    const overlay = document.getElementById('myKuppisModal');
    const body = document.getElementById('myKuppisModalBody');
    if (!overlay || !body) return;

    const tabs = body.querySelector('.kuppi-modal-tabs') ;
    if (!tabs) return;

      const hostsBtn = tabs.querySelector('#myHosts');
      const requestsBtn = tabs.querySelector('#myRequests');
      const reportsBtn = tabs.querySelector('#reports');
      const myAttendsBtn = tabs.querySelector('#myAttends');
      const myFavouritesBtn = tabs.querySelector('#myFavourites');

      // Helper: toggle active class on clicked tab
      const setActiveTab = (btn) => {
        tabs.querySelectorAll('button').forEach(b => b.classList.remove('active'));
        if (btn) {
          btn.classList.add('active');
          if (btn.id) currentTabId = btn.id;
        }
      };

      // Centralized loaders per tab
      const loaders = {
        myHosts: () => {
          window.newMyHostKuppiScroll?.resetScroll?.();
          window.newMyHostKuppiScroll?.loadNextElements?.();
        },
        myRequests: () => {
          window.newMyRequestsScroll?.resetScroll?.();
          window.newMyRequestsScroll?.loadNextElements?.();
        },
        reports: () => {
          window.newReportsKuppiScroll?.resetScroll?.();
          window.newReportsKuppiScroll?.loadNextElements?.();
        },
        myAttends: () => {
          window.newMyAttendsKuppiScroll?.resetScroll?.();
          window.newMyAttendsKuppiScroll?.loadNextElements?.();
        },
        myFavourites: () => {
          window.newMyFavouritesKuppiScroll?.resetScroll?.();
          window.newMyFavouritesKuppiScroll?.loadNextElements?.();
        }
      };

      if (!listenersBound) {
        hostsBtn && hostsBtn.addEventListener('click', (e) => {
          e.preventDefault();
          setActiveTab(hostsBtn);
          loaders.myHosts?.();
        });
        requestsBtn && requestsBtn.addEventListener('click', (e) => {
          e.preventDefault();
          setActiveTab(requestsBtn);
          loaders.myRequests?.();
        });
        reportsBtn && reportsBtn.addEventListener('click', (e) => {
          e.preventDefault();
          setActiveTab(reportsBtn);
          loaders.reports?.();
        });
        myAttendsBtn && myAttendsBtn.addEventListener('click', (e) => {
          e.preventDefault();
          setActiveTab(myAttendsBtn);
          loaders.myAttends?.();
        });
        myFavouritesBtn && myFavouritesBtn.addEventListener('click', (e) => {
          e.preventDefault();
          setActiveTab(myFavouritesBtn);
          loaders.myFavourites?.();
        });
        listenersBound = true;
      }

    overlay.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    // Load last selected tab and set it active
    const currentBtn = tabs.querySelector(`#${currentTabId}`) || hostsBtn;
    setActiveTab(currentBtn);
    loaders[currentTabId]?.();
  }

  function closeMyKuppisModal(){
    const overlay = document.getElementById('myKuppisModal');
    if (overlay) overlay.style.display = 'none';
    document.body.style.overflow = '';
  }

  window.openMyKuppisModal = openMyKuppisModal;
  window.closeMyKuppisModal = closeMyKuppisModal;
})();
