// Kuppi filters and overlay close handlers
(function(){
  document.addEventListener('DOMContentLoaded', function(){
    const kuppiModal = document.getElementById('kuppiModal');
    if (kuppiModal) {
      kuppiModal.addEventListener('click', function(e){ if (e.target === this) window.closeKuppiModal && closeKuppiModal(); });
    }
    const myKuppisModal = document.getElementById('myKuppisModal');
    if (myKuppisModal) {
      myKuppisModal.addEventListener('click', function(e){ if (e.target === this) window.closeMyKuppisModal && closeMyKuppisModal(); });
    }
    const kuppiRequestsModal = document.getElementById('kuppiRequestsModal');
    if (kuppiRequestsModal) {
      kuppiRequestsModal.addEventListener('click', function(e){ if (e.target === this) window.closeKuppiRequestsModal && closeKuppiRequestsModal(); });
    }

    const filterBtn = document.getElementById('filterBtn');
    const filterDropdown = document.getElementById('filterDropdown');
    const statusFilter = document.getElementById('statusFilter');

    if (filterBtn && filterDropdown) {
      filterBtn.addEventListener('click', function(e){
        filterDropdown.style.display = filterDropdown.style.display === 'block' ? 'none' : 'block';
        e.stopPropagation();
      });
      document.addEventListener('click', function(e){
        if (filterDropdown.style.display === 'block' && !filterDropdown.contains(e.target) && e.target !== filterBtn) {
          filterDropdown.style.display = 'none';
        }
      });
    }

    if (statusFilter) {
      statusFilter.addEventListener('change', function(){
        window.filterKuppiByStatus && filterKuppiByStatus();
        if (filterDropdown) filterDropdown.style.display = 'none';
      });
    }
  });

  function filterKuppiByStatus(){
    const selectedStatus = document.getElementById('statusFilter')?.value;
    // Target cards in My Kuppis modal (or fallback to generic class)
    const container = document.getElementById('myKuppisModalBody') || document;
    const cards = container.querySelectorAll('.kuppi-post, .kuppi-post-container');
    cards.forEach(card => {
      // Prefer explicit status element; fallback to ribbon text
      const statusElem = card.querySelector('.post-status') || card.querySelector('.kuppi-ribbon');
      const statusText = statusElem ? statusElem.textContent.trim() : '';
      if (!selectedStatus || statusText === selectedStatus) {
        card.style.display = '';
      } else {
        card.style.display = 'none';
      }
    });
  }

  window.filterKuppiByStatus = filterKuppiByStatus;
})();
