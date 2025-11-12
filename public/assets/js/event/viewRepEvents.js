
const viewEventModal = document.getElementById('viewEventsModal');
const closeViewEventsModalBtn = document.getElementById('closeViewEventsModalBtn');
const repEventListDiv = document.getElementById('repEventsList');

closeViewEventsModalBtn.addEventListener('click', () => {
        repEventScroll.resetScroll(); // Clear list on close
        viewEventModal.classList.remove('active');
});

/*
viewEventModal.addEventListener('click', (e) => {
    if (e.target === viewEventModal) {
        repEventListDiv.innerHTML = ''; // Clear list on close
        viewEventModal.classList.remove('active');
    }
});
*/

repEventListDiv.addEventListener('click', function(e) {
    const editBtn = e.target.closest('.edit-btn');
    const deleteBtn = e.target.closest('.delete-btn');

    if (editBtn){
        const row = editBtn.closest('.rep-event-row');
        //Set form values
        document.getElementById('eventTitle').value = row.querySelector('.title').textContent;
        document.getElementById('eventHeldAt').value = row.querySelector('.location').textContent;

        document.getElementById('eventForm').setAttribute('type', 'update');
        document.getElementById('eventForm').setAttribute('data-id', row.getAttribute('data-id'));
        document.getElementById('eventDescription').value = row.getAttribute('data-description');
        document.getElementById('eventDate').value = row.getAttribute('data-timestamp');

        // Enables Form
        document.getElementById('modalHeaderName').innerText = "Edit Event";
        document.getElementById('submitEventBtn').innerText = "Save Changes";

        repEventScroll.resetScroll(); // Clear list on close
        viewEventModal.classList.remove('active');
        formEventModal.classList.add('active');

    }else if(deleteBtn){
        const row = deleteBtn.closest('.rep-event-row');
        const eventId = row.getAttribute('data-id');

        if (confirm('Are you sure you want to delete this event?')) {
            Ajax.jsonPost('/event/deleteEvent', { event_id: eventId })
        }

        repEventScroll.resetScroll(); // Clear list on close
        viewEventModal.classList.remove('active');
    }


});

repEventListDiv.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        // Delete logic here
    });
});
