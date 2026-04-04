(function () {
  function renderKuppiCardActions(card, item) {
    
    const header = card.querySelector('.kuppi-header');
    let headerRight = header.querySelector('.kuppi-actions');

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

    // Bind listeners safely
    const pData = {
      participantsBtn  : participantsBtn,
      participantsText : participantsText,
      favBtn           : favBtn
    };
    bindActionListeners({ card, item, pData});

    // Render lucide icons
    if (window.lucide && typeof window.lucide.createIcons === 'function') {
      window.lucide.createIcons();
    }
  }

  function bindActionListeners({ card, item, pData}) {
    var participantsBtn = pData.participantsBtn;
    var participantsText = pData.participantsText;
    var favBtn = pData.favBtn;
    
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
/*
    if (reportBtn && !reportBtn.dataset.bound) {
      reportBtn.dataset.bound = 'true';
      reportBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        if (typeof window.openReportKuppiModal === 'function') {
          window.openReportKuppiModal(item);
        } else {
          reportBtn.dispatchEvent(new CustomEvent('kuppi:report', {
            bubbles: true,
            detail: { id: item?.id }
          }));
        }
      });
    }
      */
  }

  window.onKuppiReport = async function(item) {

    try {
      const res = await fetch('/kuppi/reportKuppi', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: item?.id }) 
      });

    } catch (err) {
      console.error('Report request error', err);
    }
  };


  window.renderKuppiCardActions = renderKuppiCardActions;
})();
