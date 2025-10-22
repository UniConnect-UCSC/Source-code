function eventCardRenderer($data){

    const title = $data.title || '';
    const universityName = $data.university_name || $data.university || '';
    const description = $data.description || '';
    const heldAt = $data.held_at || $data.location || '';
    const timestamp = $data.event_timestamp || $data.date || '';

    // Format: "D, M d, Y h:i A"
    const formatDate = (value) => {
        if (!value) return '';
        const d = new Date(value);
        if (isNaN(d)) return '';
        const weekday = d.toLocaleString('en-US', { weekday: 'short' }); // D
        const month = d.toLocaleString('en-US', { month: 'short' }); // M
        const day = String(d.getDate()).padStart(2, '0'); // d
        const year = d.getFullYear(); // Y
        let hours = d.getHours();
        const minutes = String(d.getMinutes()).padStart(2, '0'); // i
        const ampm = hours >= 12 ? 'PM' : 'AM'; // A
        hours = hours % 12;
        if (hours === 0) hours = 12;
        const hourStr = String(hours).padStart(2, '0'); // h
        return `${weekday}, ${month} ${day}, ${year} ${hourStr}:${minutes} ${ampm}`;
    };

    // Root card
    const card = document.createElement('div');
    card.className = 'event-card';

    // Image placeholder
    const image = document.createElement('div');
    image.className = 'event-image';
    card.appendChild(image);

    // Content wrapper
    const content = document.createElement('div');
    content.className = 'event-content';
    card.appendChild(content);

    // Header
    const header = document.createElement('div');
    header.className = 'event-header';
    content.appendChild(header);

    const headerLeft = document.createElement('div');
    header.appendChild(headerLeft);

    const h3 = document.createElement('h3');
    h3.className = 'event-title';
    h3.textContent = title;
    headerLeft.appendChild(h3);

    const uniSpan = document.createElement('span');
    uniSpan.className = 'event-university';
    uniSpan.textContent = universityName;
    headerLeft.appendChild(uniSpan);

    // Date
    const dateDiv = document.createElement('div');
    dateDiv.className = 'event-date';
    dateDiv.textContent = formatDate(timestamp);
    content.appendChild(dateDiv);

    // Description
    const descriptionP = document.createElement('p');
    descriptionP.className = 'event-description';
    descriptionP.textContent = description;
    content.appendChild(descriptionP);

    // Location
    const locationDiv = document.createElement('div');
    locationDiv.className = 'event-location';
    locationDiv.textContent = heldAt;
    content.appendChild(locationDiv);

    return card;
}

function repEventRenderer($data){
    const [date, time] = ($data.event_timestamp || '').split(' ');
    const title = $data.title || '';
    const heldAt = $data.held_at || '';
    const description = $data.description || '';
    const id = $data.id || ''; // Use your unique identifier field

    // Root row
    const row = document.createElement('div');
    row.className = 'rep-event-row';

    //Hidden data inside row Div
    row.setAttribute('data-id', id);
    row.setAttribute('data-description', description);
    row.setAttribute('data-timestamp', $data.event_timestamp);

    // Title cell
    const titleCell = document.createElement('div');
    titleCell.className = 'rep-event-cell title';
    titleCell.textContent = title;
    row.appendChild(titleCell);

    // Date cell
    const dateCell = document.createElement('div');
    dateCell.className = 'rep-event-cell date';
    dateCell.textContent = date || '';
    row.appendChild(dateCell);

    // Time cell
    const timeCell = document.createElement('div');
    timeCell.className = 'rep-event-cell time';
    timeCell.textContent = time || '';
    row.appendChild(timeCell);

    // Location cell
    const locationCell = document.createElement('div');
    locationCell.className = 'rep-event-cell location';
    locationCell.textContent = heldAt;
    row.appendChild(locationCell);

    // Actions cell
    const actionsCell = document.createElement('div');
    actionsCell.className = 'rep-event-cell actions';

    const editBtn = document.createElement('button');
    editBtn.className = 'icon-btn edit-btn';
    const editIcon = document.createElement('i');
    editIcon.setAttribute('data-lucide', 'pencil');
    editBtn.appendChild(editIcon);

    const deleteBtn = document.createElement('button');
    deleteBtn.className = 'icon-btn delete-btn';
    const deleteIcon = document.createElement('i');
    deleteIcon.setAttribute('data-lucide', 'trash-2');
    deleteBtn.appendChild(deleteIcon);

    actionsCell.appendChild(editBtn);
    actionsCell.appendChild(deleteBtn);

    row.appendChild(actionsCell);

    return row;
}