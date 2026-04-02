const newEventScroll = new InfinityScroll(
    'getEvents',
    '/event/scrollable',
    document.getElementById('eventsGrid'),
    eventCardRenderer,
    0,
    2
);



const repEventScroll = new InfinityScroll(
    'getRepEvents',
    '/event/scrollable',
    document.getElementById('repEventsList'),
    repEventRenderer,
    0,
    100
);

const categoryScroll = new InfinityScroll(
    'getCategories',
    '/event/scrollable',
    document.getElementById('categoriesSection'),
    categoryRenderer,
    0,
    10
);

const formCategorySuggestionScroll = new InfinityScroll(
    'getCategories',
    '/event/scrollable',
    document.getElementById('categorySuggestions'),
    categorySuggestionRenderer,
);

const searchEventScroll = new InfinityScroll(
    'getEventSuggestions',
    '/event/scrollable',
    document.getElementById('searchSuggestions'),
    searchSuggestionRenderer,
    0,
    5
);