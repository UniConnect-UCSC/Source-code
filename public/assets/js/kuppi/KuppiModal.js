// Detail Kuppi modal: open + close
(function(){
  function openKuppiModal(card) {
    const topic = card.querySelector('.post-topic')?.innerText || '';
    const university = card.querySelector('.post-university')?.innerText || '';
    const datetime = card.querySelector('.post-datetime')?.innerText || '';
    let date = '', time = '';
    if (datetime.includes(' ')) {
      [date, time] = datetime.split(' ');
    }
    let platform = card.querySelector('.post-platform')?.innerText || '';
    platform = platform.replace('Platform: ', '');
    let link = card.dataset?.link || '';
    const hostName = card.dataset?.hostName || '';
    if (link && !/^https?:\/\//i.test(link)) link = 'https://' + link;
    const image = card.querySelector('.post-image img')?.src || '';

    const body = document.getElementById('kuppiModalBody');
    if (!body) return;
    body.innerHTML = `
      <h2>${topic}</h2>
      ${image ? `<img src="${image}" alt="${topic}" style="width:100%;margin-top:12px;border-radius:8px;">` : ''}
      <div class="kuppi-details kuppi-details-compact">
        <div class="kuppi-detail-value kuppi-host-row">
          <span>${hostName || '<span style="color:#9ca3af">Unknown</span>'}</span>
          <span class="kuppi-profile-mini" aria-hidden="true"></span>
        </div>
        <div class="kuppi-detail-value">${university}</div>
        <div class="kuppi-detail-value">${date} ${time}</div>
        <div class="kuppi-detail-value">${platform}</div>
        <div class="kuppi-detail-value kuppi-link-row">${link ? `<a href="${link}" target="_blank" rel="noopener noreferrer">${link}</a>` : '<span style="color:#9ca3af">Not provided</span>'}</div>
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
