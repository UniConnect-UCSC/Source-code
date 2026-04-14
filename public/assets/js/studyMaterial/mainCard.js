(function() {
    const smGrid = document.getElementById('sm-grid');

    mainStudyMaterialScroll.setupAutoLoadOnScroll();
    mainStudyMaterialScroll.loadNextElements();

    // mainStudyMaterialScroll.setContextProvider(() => {
    //     return {
    //     };
    // });

    smGrid.addEventListener('sm:view-click', (e) => {
        const { id } = e.detail;
        console.log(`View button clicked for study material ID: ${id}`);
        // Implement the logic to open the study material detail view or perform any desired action
    });

    mainStudyMaterialScroll.addEventListener('successfulLoad', () => {
        lucide.createIcons();
    });

})();


