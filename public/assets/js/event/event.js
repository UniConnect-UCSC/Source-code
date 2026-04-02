const createEventBtn = document.getElementById('createEventBtn');
const viewEventBtn = document.getElementById('viewEventBtn');
const filterButtons = document.querySelectorAll('#filterButtons .btn');

// For hiding body scroll when modal is open
document.body.classList.add('body-class');

// Modal controls
createEventBtn.addEventListener('click', () => {
    document.getElementById('modalHeaderName').innerText = "Create New Event";
    document.getElementById('submitEventBtn').innerText = "Create Event";
    document.getElementById('eventForm').setAttribute('type', 'create');
    formEventModal.classList.add('active');
});

viewEventBtn.addEventListener('click', () => {
    (async () => {
        
        if(await repEventScroll.loadNextElements()){
            lucide.createIcons();
            document.getElementById('repEventsEmptyMessage').style.display = 'none';
        }else{
            document.getElementById('repEventsEmptyMessage').style.display = 'block'; 
        }

        lucide.createIcons();
    })();

    viewEventModal.classList.add('active');
});


filterButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        filterButtons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentFilter = btn.dataset.filter;
    
        // Implement filter logic here

    });
});


