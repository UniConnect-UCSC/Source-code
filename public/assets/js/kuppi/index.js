
  const newMainKuppiScroll = new InfinityScroll(
    'getAllKuppies',
    '/kuppi/scrollable',
    document.getElementById("kuppi-container"),
    window.renderMainKuppiCards,
    0,
    4
  );
  newMainKuppiScroll.loadNextElements();

  const newMyHostKuppiScroll = new InfinityScroll(
    'getMyHostKuppies',
    '/kuppi/scrollable',
    document.getElementById("my-kuppi-content"),
    window.renderMyKuppiCards,
    0,
    4
  );
  window.newMyHostKuppiScroll = newMyHostKuppiScroll;

  const newMyRequestsScroll = new InfinityScroll (
    'getMyRequests',
    '/kuppi/scrollable',
    document.getElementById("my-kuppi-content"),
    window.renderMyRequestsCards,
    0,
    4
  );
  window.newMyRequestsScroll = newMyRequestsScroll;

  const newKuppiRequestsScroll = new InfinityScroll(
    'getKuppiRequests',
    '/kuppi/scrollable',
    document.getElementById("kuppi-requests-content"),
    window.renderKuppiRequestsCards,
    0,
    4
  );
  window.newKuppiRequestsScroll = newKuppiRequestsScroll;

 newMainKuppiScroll.setupAutoLoadOnScroll();
 newMainKuppiScroll.addEventListener('successfulLoad' , () => {
  lucide.createIcons();
 });

  

