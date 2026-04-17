(function(){
  // Alias if only onKuppiReport exists
  if (typeof window.reportKuppi !== 'function' && typeof window.onKuppiReport === 'function') {
    window.reportKuppi = window.onKuppiReport;
  }

  let overlayClickHandler = null;

  function openReportKuppiModal(item) {
    const modal = document.getElementById('reportKuppiModal');
    if (!modal) return;

    window._reportKuppiItem = item;
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    const confirmBtn = document.getElementById('confirmReportKuppi');
    if (confirmBtn) {
      confirmBtn.onclick = function (e) {
        e.stopPropagation();

        Ajax.jsonPost('/kuppi/reportKuppi', { kuppi_id: item.id }).then((response) => {
          if (response && response.success) {
            window.newMainKuppiScroll.resetScroll();
            window.newMainKuppiScroll.loadNextElements();
            setTimeout(closeReportKuppiModal, 600);
          } else {
            console.log(response);
          }
        });
      };
    }

    overlayClickHandler = function(e){
      if (e.target === modal) closeReportKuppiModal();
    };
    modal.addEventListener('click', overlayClickHandler);
  }

  function closeReportKuppiModal() {
    const modal = document.getElementById('reportKuppiModal');
    if (modal) modal.style.display = 'none';
    document.body.style.overflow = '';
    window._reportKuppiItem = null;


    if (modal && overlayClickHandler) {
      modal.removeEventListener('click', overlayClickHandler);
      overlayClickHandler = null;
    }
  }

  window.openReportKuppiModal = openReportKuppiModal;
  window.closeReportKuppiModal = closeReportKuppiModal;
})();