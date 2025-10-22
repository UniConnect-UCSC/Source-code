const newEventScroll = new InfinityScroll(
    'getEvents',
    '/event/scrollable',
    document.getElementById('eventsGrid'),
    eventCardRenderer,
    0,
    100
);
newEventScroll.loadNextElements();

const repEventScroll = new InfinityScroll(
    'getRepEvents',
    '/event/scrollable',
    document.getElementById('repEventsList'),
    repEventRenderer,
    0,
    100
);

