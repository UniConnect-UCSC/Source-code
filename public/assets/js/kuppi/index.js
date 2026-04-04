
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

  newMyHostKuppiScroll.setContextProvider(() => {
    const activeBtn = document.querySelector('#my-hosts-filters button.active[data-status-filter]');
    const status = activeBtn ? activeBtn.getAttribute('data-status-filter') : '';
    return status ? { status } : {};
  });

  const newMyRequestsScroll = new InfinityScroll (
    'getMyRequests',
    '/kuppi/scrollable',
    document.getElementById("my-kuppi-content"),
    window.renderMyRequestsCards,
    0,
    4
  );
  window.newMyRequestsScroll = newMyRequestsScroll;

  newMyRequestsScroll.setContextProvider(() => {
    const activeBtn = document.querySelector('#my-requests-filters button.active[data-status-filter]');
    const status = activeBtn ? activeBtn.getAttribute('data-status-filter') : '';
    return status ? { status } : {};
  });

  const newKuppiRequestsScroll = new InfinityScroll(
    'getKuppiRequests',
    '/kuppi/scrollable',
    document.getElementById("kuppi-requests-content"),
    window.renderKuppiRequestsCards,
    0,
    4
  );
  window.newKuppiRequestsScroll = newKuppiRequestsScroll;

  const newMyParticipationsScroll = new InfinityScroll(
    'getMyParticipations',
    '/kuppi/scrollable',
    document.getElementById("my-kuppi-content"),
    window.renderMyParticipationCards,
    0,
    4
  );
  window.newMyParticipationsScroll = newMyParticipationsScroll;

  // Provide the active status filter as context for My Attends requests
  newMyParticipationsScroll.setContextProvider(() => {
    const activeBtn = document.querySelector('#my-attends-filters button.active[data-status-filter]');
    const status = activeBtn ? activeBtn.getAttribute('data-status-filter') : '';
    return status ? { status } : {};
  });

  const newMyFavoritesScroll = new InfinityScroll(
    'getMyFavorites',
    '/kuppi/scrollable',
    document.getElementById("my-kuppi-content"),
    window.renderMyFavoriteCards,
    0,
    4
  );
  window.newMyFavoritesScroll = newMyFavoritesScroll;

  newMyFavoritesScroll.setContextProvider(() => {
    const activeBtn = document.querySelector('#my-favourites-filters button.active[data-status-filter]');
    const status = activeBtn ? activeBtn.getAttribute('data-status-filter') : '';
    return status ? { status } : {};
  });

 newMainKuppiScroll.setupAutoLoadOnScroll();
 newMainKuppiScroll.addEventListener('successfulLoad' , () => {
  lucide.createIcons();
 });

 newMyHostKuppiScroll.setupAutoLoadOnScroll();
 newMyRequestsScroll.setupAutoLoadOnScroll();
 newMyParticipationsScroll.setupAutoLoadOnScroll();
 newMyFavoritesScroll.setupAutoLoadOnScroll();

  

