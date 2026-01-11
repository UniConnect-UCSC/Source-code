// Detail Kuppi modal: open + close
(function(){
  function openKuppiModal(card) {
    // Support both legacy (post-*) and new (kuppi-*) class structures
    const topic = card.querySelector('.kuppi-title')?.innerText;
    const university = card.querySelector('.kuppi-university')?.innerText;
    const datetime = card.querySelector('.kuppi-date')?.innerText;
    let date = '', time = '';
    if (datetime.includes(' ')) {
      [date, time] = datetime.split(' ');
    }
    let platformText = card.querySelector('.kuppi-location')?.innerText;
    let platform = platformText.replace(/^Platform:\s*/i, '');
    let link = card.dataset?.link || '';
    const hostName = card.dataset?.hostName;
    const requesterName = card.getAttribute('data-requester-name')
      || card.dataset?.requesterName
      || card.querySelector('.post-requester-name')?.innerText
      || '';
    if (link && !/^https?:\/\//i.test(link)) link = 'https://' + link;
    const image = card.querySelector('.kuppi-image img')?.src;
    const category = card.dataset?.category
      || card.querySelector('.post-category')?.innerText
      || '';

    const body = document.getElementById('kuppiModalBody');
    if (!body) return;
    body.innerHTML = `
      <h2>${topic}</h2>
      <div class="kuppi-modal-layout">
        ${image ? `<div class="kuppi-modal-image"><img src="${image}" alt="${topic}"></div>` : ''}
        <div class="kuppi-modal-side">
          <div class="kuppi-details kuppi-details-compact">
            ${requesterName ? `<div class="kuppi-detail-value">Requested by <strong>${requesterName}</strong></div>` : ''}
            ${hostName ? `<div class="kuppi-detail-value kuppi-host-row"><span>Hosted by <strong>${hostName}</strong></span><span class="kuppi-profile-mini" aria-hidden="true"></span></div>` : ''}
            ${university ? `<div class="kuppi-detail-value"><span class="kuppi-university">${university}</span></div>` : ''}
            ${(date || time) ? `<div class="kuppi-detail-value kuppi-meta">${date} ${time}</div>` : ''}
            ${category ? `<div class="kuppi-detail-value">Category: ${category}</div>` : ''}
            ${platform ? `<div class="kuppi-detail-value kuppi-meta">Platform: ${platform}</div>` : ''}
            <div class="kuppi-detail-value kuppi-link-row">${link ? `<a href="${link}" target="_blank" rel="noopener noreferrer">${link}</a>` : '<span style="color:#9ca3af">Not provided</span>'}</div>
          </div>
        </div>
      </div>
    `;
    const modal = document.getElementById('kuppiModal');
    if (modal) modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
  }

  function closeKuppiModal(){
    const overlay = document.getElementById('kuppiModal');
    if (overlay) overlay.style.display = 'none';
    document.body.style.overflow = '';
    // Restore My Kuppis overlay if it was previously open
    if (window._restoreMyKuppisAfterEdit) {
      const myOverlay = document.getElementById('myKuppisModal');
      if (myOverlay) {
        myOverlay.style.display = 'flex';
      }
      window._restoreMyKuppisAfterEdit = false;
    }
  }

  window.openKuppiModal = openKuppiModal;
  window.closeKuppiModal = closeKuppiModal;
})();
