const viewEventModal = document.getElementById('viewEventsModal');
const closeViewEventsModalBtn = document.getElementById('closeViewEventsModalBtn');
const repEventListDiv = document.getElementById('repEventsList');

closeViewEventsModalBtn.addEventListener('click', () => {
        repEventScroll.resetScroll(); // Clear list on close
        viewEventModal.classList.remove('active');
});

repEventScroll.setupAutoLoadOnScroll();

repEventScroll.addEventListener('successfulLoad', () => {
    lucide.createIcons(); 
});

repEventListDiv.addEventListener('click', function(e) {
    const editBtn = e.target.closest('.edit-btn');
    const deleteBtn = e.target.closest('.delete-btn');

    if (editBtn){
        const row = editBtn.closest('.rep-event-row');

        Ajax.jsonPost('/event/getAllCategoriesForEvent', { event_id: row.getAttribute('data-id') }).then(
            (response) => {
                if(response.success){
                    const categoriesReadyEvent = new CustomEvent('categoriesReady', { detail: response.categories });
                    formEventModal.dispatchEvent(categoriesReadyEvent);
                }else{
                    console.error('Failed to fetch categories for event:', response.message);
                }
            }
        )

        //Set form values
        document.getElementById('eventTitle').value = row.querySelector('.title').textContent;
        document.getElementById('eventHeldAt').value = row.querySelector('.location').textContent;

        document.getElementById('eventForm').setAttribute('type', 'update');
        document.getElementById('eventForm').setAttribute('data-id', row.getAttribute('data-id'));
        document.getElementById('eventDescription').value = row.getAttribute('data-description');

        let raw = row.getAttribute('data-timestamp'); // e.g. "2024-04-02 15:30:00"
        let formatted = raw.replace(' ', 'T').slice(0, 16); // "2024-04-02T15:30"
        document.getElementById('eventDate').value = formatted;

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

