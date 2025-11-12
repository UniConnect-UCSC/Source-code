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
 
    const formData = new FormData(eventForm);

    var url = '';
    if (eventForm.getAttribute('type') === 'update') {
        formData.append('event_id', eventForm.getAttribute('data-id'));
        url = '/event/updateEvent';
    } else if (eventForm.getAttribute('type') === 'create') {
        url = '/event/createNewEvent';
    }

    // Send FormData (do not set Content-Type, browser will handle it)

    console.log('Form data being sent:', ...formData.entries());

    const $response = await Ajax.formDataPost(url, formData);

    if ($response['status'] === 'success') {
        console.log('Event successfully created/updated');
    } else {
        console.error('Error creating event:', $response);
    }

    eventForm.reset();
    formEventModal.classList.remove('active');
});


