// My Kuppis modal
(function(){
  function openMyKuppisModal(){
    const overlay = document.getElementById('myKuppisModal');
    const body = document.getElementById('myKuppisModalBody');
    if (!overlay || !body){
      return ;
    } 

    const list = body.querySelector('.kuppi-modal-list');

    overlay.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    
    window.newMyKuppiScroll.loadNextElements() ;
  }

  function closeMyKuppisModal(){
    const overlay = document.getElementById('myKuppisModal');
    if (overlay) overlay.style.display = 'none';
    document.body.style.overflow = '';
  }
  window.openMyKuppisModal = openMyKuppisModal;
  window.closeMyKuppisModal = closeMyKuppisModal;
})();
