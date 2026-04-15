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

        window.location.href = `/studymaterial/${id}`;
    });

    smGrid.addEventListener('sm:share-click', async (e) => {
        const { id } = e.detail;
        const url = `${window.location.origin}/studymaterial/${id}`;

        try {
            await navigator.clipboard.writeText(url);
            alert('Link copied to clipboard!');
        } catch (err) {
            console.error('Failed to copy: ', err);
            alert('Failed to copy link');
        }
    });

    mainStudyMaterialScroll.addEventListener('successfulLoad', () => {
        lucide.createIcons();
    });

})();


