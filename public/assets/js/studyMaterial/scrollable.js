const mainStudyMaterialScroll = new InfinityScroll(
    'getStudyMaterials',
    '/studymaterial/scrollable',
    document.getElementById('sm-grid'),
    smCardRenderer,
    0,
    10 
);

const smFormCategoryScroll = new InfinityScroll(
    'getCategories',
    '/studymaterial/scrollable',
    document.getElementById('categorySuggestion'),
    smFormSuggestionRenderer,
    0,
    5 
);