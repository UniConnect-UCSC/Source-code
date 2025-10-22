const formEventModal = document.getElementById('formEventModal');
const closeModalBtn = document.getElementById('closeModalBtn');
const eventForm = document.getElementById('eventForm');

closeModalBtn.addEventListener('click', () => {
    formEventModal.classList.remove('active');
    eventForm.reset();
});

/*
formEventModal.addEventListener('click', (e) => {
    if (e.target === formEventModal) {
        formEventModal.classList.remove('active');
        eventForm.reset(); 
    }
});
*/

eventForm.addEventListener('submit', async (e) => {
    e.preventDefault();
 
    const newEvent = {
        title: document.getElementById('eventTitle').value,
        event_timestamp: document.getElementById('eventDate').value,
        held_at: document.getElementById('eventHeldAt').value,
        description: document.getElementById('eventDescription').value
    };

    var url = '';
    if(eventForm.getAttribute('type') === 'update'){
        newEvent.event_id = eventForm.getAttribute('data-id');
        url = '/event/updateEvent';

    }else if(eventForm.getAttribute('type') === 'create'){
        url = '/event/createNewEvent';
    }

    $response = await Ajax.post(url, newEvent);

    if($response['status'] === 'success'){
        console.log('Event successfully created/updated');
    } else {
        // Optionally show an error message
        console.error('Error creating event:', $response);
    }

    eventForm.reset(); //clear form
    formEventModal.classList.remove('active');
});


