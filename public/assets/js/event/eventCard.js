(function() {

const eventGrid = document.getElementById('eventsGrid');
var runningFavToggles = {};
var runningPartToggle = {};

newEventScroll.setupAutoLoadOnScroll();
newEventScroll.loadNextElements();

newEventScroll.setContextProvider(() => {
    return {
        filterCategories: window.selectedEventFilterCategories || [],
        searchTerm: window.selectedEventSearchQuery || '',
        onlyFavorites: window.selectedEventFavoritesOnly || false
    };
});


eventGrid.addEventListener('event:participants-click', (e) => {
    const { eventId, current, element } = e.detail;
    console.log(`Participants button clicked for event ID: ${eventId}, currently active: ${current}`);

    if(runningPartToggle[eventId]) {
        console.log('Toggle already in progress for this event. Ignoring duplicate request.');
        e.stopPropagation();
        return;
    }
    
    runningPartToggle[eventId] = true;
    const data = {
        event_id: eventId,
        current_status: current,
        action: "participate"
    };

    Ajax.jsonPost('/event/toggle', data).then((response) => {
        if(response.newStatus === !current) {
            console.log(`participant button successfully toggled to: ${response.newStatus}`);
            element.classList.toggle('active'); 
            element.querySelector('.participants-count').textContent = response.participantCount;

        } else {
            console.error('Server response inconsistent with requested toggle action.');
            console.log(response);
            //log Error to server
        }

        runningPartToggle[eventId] = false;
    });
    e.stopPropagation();
});  

eventGrid.addEventListener('event:favorite-toggle', (e) => {
    const { eventId, current, element } = e.detail;
    console.log(`Favorite toggle clicked for event ID: ${eventId}, currently active: ${current}`);

    if(runningFavToggles[eventId]) {
        console.log('Toggle already in progress for this event. Ignoring duplicate request.');
        e.stopPropagation();
        return;
    }

    runningFavToggles[eventId] = true;

    const data = {
        event_id: eventId,
        current_status: current,
        action: "favorite"
    };

    Ajax.jsonPost('/event/toggle', data).then((response) => {
        if(response.newStatus === !current) {
            console.log(`Favorite status successfully toggled to: ${response.newStatus}`);
            element.classList.toggle('active'); 

        } else {
            console.error('Server response inconsistent with requested toggle action.');
            console.log(response);
            //log Error to server
        }

        runningFavToggles[eventId] = false;
    }
    );
    
    e.stopPropagation();
});

newEventScroll.addEventListener('successfulLoad', () => {
    lucide.createIcons();
});

})();