(function () {
  function renderKuppiCardActions(card, item) {
    // Find or create the right-side header actions container
    const header = card.querySelector('.kuppi-header') || card.querySelector('.event-header') || card;
    let headerRight = header.querySelector('.kuppi-actions');
    if (!headerRight) {
      headerRight = document.createElement('div');
      headerRight.className = 'kuppi-actions';
      header.appendChild(headerRight);
    }

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
    const count = Number(item?.participants_count ?? item?.participants ?? 0);
    participantsText.textContent = String(isFinite(count) ? count : 0);
    participantsBtn.append(participantsIcon, participantsText);

    // Favorite heart button
    const favBtn = document.createElement('button');
    const isFavorite = Boolean(item?.is_favorite ?? item?.favorite);
    favBtn.className = 'icon-btn favorite-btn' + (isFavorite ? ' active' : '');
    favBtn.type = 'button';
    favBtn.setAttribute('aria-pressed', isFavorite ? 'true' : 'false');
    favBtn.setAttribute('aria-label', isFavorite ? 'Unfavorite kuppi' : 'Favorite kuppi');
    if (item && item.id) favBtn.dataset.kuppiId = item.id;

    const favIcon = document.createElement('i');
    favIcon.setAttribute('data-lucide', 'heart');
    favBtn.appendChild(favIcon);

    // Report button
    const reportBtn = document.createElement('button');
    reportBtn.className = 'icon-btn report-btn';
    reportBtn.type = 'button';
    reportBtn.setAttribute('aria-label', 'Report kuppi');
    if (item && item.id) reportBtn.dataset.kuppiId = item.id;

    const reportIcon = document.createElement('i');
    reportIcon.setAttribute('data-lucide', 'flag');
    reportBtn.appendChild(reportIcon);

    // Inject actions
    headerRight.append(participantsBtn, favBtn, reportBtn);

    // Bind listeners safely
    bindActionListeners({ card, item, participantsBtn, participantsText, favBtn, reportBtn });

    // Render lucide icons
    if (window.lucide && typeof window.lucide.createIcons === 'function') {
      window.lucide.createIcons();
    }
  }

  function bindActionListeners({ card, item, participantsBtn, participantsText, favBtn, reportBtn }) {
    if (participantsBtn && !participantsBtn.dataset.bound) {
      participantsBtn.dataset.bound = 'true';
      participantsBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        const current = Number(participantsText.textContent || '0');
        const next = isFinite(current) ? current + 1 : 1;
        participantsText.textContent = String(next);

        if (typeof window.onKuppiParticipate === 'function') {
          window.onKuppiParticipate(item);
        } else {
          participantsBtn.dispatchEvent(new CustomEvent('kuppi:participants-click', {
            bubbles: true,
            detail: { id: item?.id, count: next }
          }));
        }
      });
    }

    if (favBtn && !favBtn.dataset.bound) {
      favBtn.dataset.bound = 'true';
      favBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        const next = !favBtn.classList.contains('active');
        favBtn.classList.toggle('active');
        favBtn.setAttribute('aria-pressed', next ? 'true' : 'false');

        if (typeof window.onKuppiFavorite === 'function') {
          window.onKuppiFavorite(item, favBtn, next);
        } else {
          favBtn.dispatchEvent(new CustomEvent('kuppi:favorite-toggle', {
            bubbles: true,
            detail: { id: item?.id, favorite: next }
          }));
        }
      });
    }

    if (reportBtn && !reportBtn.dataset.bound) {
      reportBtn.dataset.bound = 'true';
      reportBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        if (typeof window.onKuppiReport === 'function') {
          window.onKuppiReport(item);
        } else {
          reportBtn.dispatchEvent(new CustomEvent('kuppi:report', {
            bubbles: true,
            detail: { id: item?.id }
          }));
        }
      });
    }
  }

  // Expose for use in the card renderer
  window.renderKuppiCardActions = renderKuppiCardActions;
})();