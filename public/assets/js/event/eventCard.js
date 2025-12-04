(function() {

const eventGrid = document.getElementById('eventsGrid');
var runningFavToggles = {};


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
    )
    
    e.stopPropagation();
});

})();