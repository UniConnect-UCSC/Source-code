(function () {
  function closeAllKuppiMenus(exceptMenuWrap) {
    document.querySelectorAll('.kuppi-card-menu.is-open').forEach(function (menuWrap) {
      if (exceptMenuWrap && menuWrap === exceptMenuWrap) return;
      menuWrap.classList.remove('is-open');
      var trigger = menuWrap.querySelector('.kuppi-card-menu-btn');
      if (trigger) trigger.setAttribute('aria-expanded', 'false');
    });
  }

  function bindGlobalKuppiMenuCloser() {
    if (window.__kuppiMenuOutsideHandlerBound) return;
    document.addEventListener('click', function (e) {
      if (e.target.closest('.kuppi-card-menu')) return;
      closeAllKuppiMenus();
    });
    window.__kuppiMenuOutsideHandlerBound = true;
  }

  function renderKuppiCardActions(card, item) {
    
    const header = card.querySelector('.kuppi-header');
    let headerRight = header.querySelector('.kuppi-actions');
    const menuWrap = headerRight.querySelector('.kuppi-card-menu');
    const menuBtn = headerRight.querySelector('.kuppi-card-menu-btn');
    const menuDropdown = headerRight.querySelector('.kuppi-card-menu-dropdown');
    const reportBtn = headerRight.querySelector('.kuppi-card-report-btn');

    // Participants pill button
    const participantsBtn = document.createElement('button');
    participantsBtn.className = 'pill-btn participants-btn';
    participantsBtn.type = 'button';
    participantsBtn.setAttribute('aria-label', 'View participants');
    if (item && item.id) participantsBtn.dataset.kuppiId = item.id;

    const participantsIcon = document.createElement('i');
    participantsIcon.setAttribute('data-lucide', 'users');

    const participantsText = document.createElement('span');
    participantsText.className = 'participants-count';
    const count = Number(item.participants ?? 0);
    participantsText.textContent = String(isFinite(count) ? count : 0);
    
    if(item.is_participating) {
      participantsBtn.classList.add('active');
    }
    participantsBtn.append(participantsIcon, participantsText);

    const favBtn = document.createElement('button');
    favBtn.className = 'pill-btn fav-btn';
    favBtn.type = 'button';
    const isFavorite = Boolean(item?.is_favorite ?? item?.favorite ?? false);
    if (isFavorite) {
      favBtn.classList.add('active');
    }
    favBtn.setAttribute('aria-pressed', isFavorite ? 'true' : 'false');
    favBtn.setAttribute('aria-label', isFavorite ? 'Unfavorite kuppi' : 'Favorite kuppi');
    if(item && item.id) favBtn.dataset.kuppiId =item.id;

    const favIcon = document.createElement('i');
    favIcon.setAttribute('data-lucide','heart');
    favBtn.appendChild(favIcon);

/*
    // Report button
    const reportBtn = document.createElement('button');
    reportBtn.className = 'icon-btn report-btn';
    reportBtn.type = 'button';
    reportBtn.setAttribute('aria-label', 'Report kuppi');
    if (item && item.id) reportBtn.dataset.kuppiId = item.id;

    const reportIcon = document.createElement('i');
    reportIcon.setAttribute('data-lucide', 'flag');
    reportBtn.appendChild(reportIcon);
*/
    // Inject actions
    headerRight.append(participantsBtn);
    headerRight.append(favBtn);
    if (menuWrap) {
      // Keep the three-dot menu as the right-most control.
      headerRight.append(menuWrap);
    }

    // Bind listeners safely
    const pData = {
      participantsBtn  : participantsBtn,
      participantsText : participantsText,
      favBtn           : favBtn,
      menuWrap         : menuWrap,
      menuBtn          : menuBtn,
      menuDropdown     : menuDropdown,
      reportBtn        : reportBtn
    };
    bindActionListeners({ card, item, pData});
    bindGlobalKuppiMenuCloser();

    // Render lucide icons
    if (window.lucide && typeof window.lucide.createIcons === 'function') {
      window.lucide.createIcons({ root: headerRight });
    }
  }

  function bindActionListeners({ card, item, pData}) {
    var participantsBtn = pData.participantsBtn;
    var participantsText = pData.participantsText;
    var favBtn = pData.favBtn;
    var menuWrap = pData.menuWrap;
    var menuBtn = pData.menuBtn;
    var menuDropdown = pData.menuDropdown;
    var reportBtn = pData.reportBtn;

    if (menuWrap && !menuWrap.dataset.bound) {
      menuWrap.dataset.bound = 'true';
      menuWrap.addEventListener('click', function (e) {
        e.stopPropagation();
      });
    }

    if (menuBtn && !menuBtn.dataset.bound) {
      menuBtn.dataset.bound = 'true';
      menuBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        var shouldOpen = !menuWrap.classList.contains('is-open');
        closeAllKuppiMenus(menuWrap);
        menuWrap.classList.toggle('is-open', shouldOpen);
        menuBtn.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
      });
    }

    if (menuDropdown && !menuDropdown.dataset.bound) {
      menuDropdown.dataset.bound = 'true';
      menuDropdown.addEventListener('click', function (e) {
        e.stopPropagation();
      });
    }

    if (reportBtn && !reportBtn.dataset.bound) {
      reportBtn.dataset.bound = 'true';
      reportBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        closeAllKuppiMenus();
        if (typeof window.openReportKuppiModal === 'function') {
          window.openReportKuppiModal(item);
        } else {
          console.warn('openReportKuppiModal is not available.');
        }
      });
    }
    
    if (participantsBtn && !participantsBtn.dataset.bound) {
      participantsBtn.dataset.bound = 'true';
      participantsBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        console.log(`Participate btn clicked on ${item.topic} `);

    const kuppiId = item.id;  
    const current = item.is_participating;
    const data = {
        kuppi_id: kuppiId,
        current_status: current,
        action: "participate"
    };
  
    Ajax.jsonPost('/kuppi/toggle', data).then((response) => {
        if(response.newStatus === !current) {
          console.log(`participant button successfully toggled to: ${response.newStatus}`);
          participantsBtn.classList.toggle('active'); 
          participantsText.textContent = response.participantCount;
          item.is_participating = response.newStatus;

        } else {
            console.error('Server response inconsistent with requested toggle action.');
            console.log(response);
        }

    });
      });
    }

    if (favBtn && !favBtn.dataset.bound) {
      favBtn.dataset.bound = 'true';
      favBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        const kuppiId = item.id;
        const current = Boolean(item?.is_favorite ?? item?.favorite ?? favBtn.classList.contains('active'));
        const data = {
          kuppi_id: kuppiId,
          current_status: current,
          action: "favorite"
        };

        Ajax.jsonPost('/kuppi/toggle', data).then((response) => {
          if (response.newStatus === !current) {
            favBtn.classList.toggle('active', response.newStatus);
            favBtn.setAttribute('aria-pressed', response.newStatus ? 'true' : 'false');
            favBtn.setAttribute('aria-label', response.newStatus ? 'Unfavorite kuppi' : 'Favorite kuppi');
            item.is_favorite = response.newStatus;
          } else {
            console.error('Server response inconsistent with favorite toggle action.');
            console.log(response);
          }
        }).catch((err) => {
          console.error('Favorite toggle failed', err);
        });
      });
    }
  }

  window.onKuppiReport = async function(item) {

    try {
      const res = await fetch('/kuppi/reportKuppi', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ kuppi_id: item?.id }) 
      });

    } catch (err) {
      console.error('Report request error', err);
    }
  };


  window.renderKuppiCardActions = renderKuppiCardActions;
})();
