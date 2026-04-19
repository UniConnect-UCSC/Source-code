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

        const body = document.getElementById('reportKuppiModalBody');
        let msgEl = document.getElementById('reportKuppiFeedback');
        if (!msgEl && body) {
          msgEl = document.createElement('p');
          msgEl.id = 'reportKuppiFeedback';
          msgEl.style.marginTop = '10px';
          msgEl.style.fontWeight = '600';
          body.appendChild(msgEl);
        }

        const setMsg = (text, ok) => {
          if (!msgEl) return;
          msgEl.textContent = text || '';
          msgEl.style.color = ok ? '#16a34a' : '#dc2626';
        };

        setMsg('', false);
        confirmBtn.disabled = true;

        Ajax.jsonPost('/kuppi/reportKuppi', { kuppi_id: item.id })
          .then((response) => {
            setMsg(response?.message || 'Kuppi reported successfully', true);
            if (response && response.success) {
              window.newMainKuppiScroll.resetScroll();
              window.newMainKuppiScroll.loadNextElements();
              setTimeout(closeReportKuppiModal, 700);
            }
          })
          .catch((err) => {
            const serverMsg = err?.data?.message || err?.message || 'Failed to report kuppi';
            setMsg(serverMsg, false);
          })
          .finally(() => {
            confirmBtn.disabled = false;
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