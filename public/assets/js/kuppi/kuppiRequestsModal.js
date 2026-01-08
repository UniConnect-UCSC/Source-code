// Kuppi Requests modal
(function(){
  function openKuppiRequestsModal(){
    const overlay = document.getElementById('kuppiRequestsModal');
    const body = document.getElementById('kuppiRequestsModalBody');
    if(!overlay || !body){ console.log('Error loading Kuppirequest modal'); return; }

    const list = body.querySelector('.kuppi-modal-list');

    overlay.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    window.newKuppiRequestsScroll.loadNextElements() ;
  }
  
  function closeKuppiRequestsModal(){
    const overlay = document.getElementById('kuppiRequestsModal');
    if (overlay) overlay.style.display = 'none';
    document.body.style.overflow = '';
  }
  window.openKuppiRequestsModal = openKuppiRequestsModal;
  window.closeKuppiRequestsModal = closeKuppiRequestsModal;
})();
