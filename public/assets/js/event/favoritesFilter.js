const favoriteBtn = document.getElementById('viewFavoritesBtn');

favoriteBtn.addEventListener('click', () => {
    favoriteBtn.classList.toggle('active');
    window.selectedEventFavoritesOnly = favoriteBtn.classList.contains('active');
    newEventScroll.refresh();
});

window.selectedEventFavoritesOnly = false; 