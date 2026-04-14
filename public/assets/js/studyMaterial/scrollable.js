const mainStudyMaterialScroll = new InfinityScroll(
    'getStudyMaterials',
    '/studymaterial/scrollable',
    document.getElementById('sm-grid'),
    smCardRenderer,
    0,
    10 
);
