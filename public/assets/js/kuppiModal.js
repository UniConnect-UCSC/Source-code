function openKuppiModal(card) {
    const topic = card.querySelector('.post-topic')?.innerText || '';
    const university = card.querySelector('.post-university')?.innerText || '';
    // Split date and time if needed
    const datetime = card.querySelector('.post-datetime')?.innerText || '';
    let date = '', time = '';
    if (datetime.includes('|')) {
        [date, time] = datetime.split('|').map(s => s.trim());
    }
    const platform = card.querySelector('.post-platform')?.innerText || '';
    const image = card.querySelector('.post-image img')?.src || '';

    document.getElementById('kuppiModalBody').innerHTML = `
        <h2>${topic}</h2>
        <p><strong>University:</strong> ${university}</p>
        <p><strong>Date:</strong> ${date}</p>
        <p><strong>Time:</strong> ${time}</p>
        <p><strong>${platform}</strong></p>
        ${image ? `<img src="${image}" alt="${topic}" style="width:100%;margin-top:12px;border-radius:8px;">` : ''}
    `;
    document.getElementById('kuppiModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeKuppiModal() {
    document.getElementById('kuppiModal').style.display = 'none';
    document.body.style.overflow = '';
}

function openHostKuppiModal(kuppiCategories) {
    let categoryOptions = '';
    kuppiCategories.forEach(category => {
        categoryOptions += `<option value="${category.id}">${category.category_name}</option>`;
    });

    document.getElementById('kuppiModalBody').innerHTML = `
        <h2>Host a Kuppi Session</h2>
        <form action="/kuppi/create" method="POST">
            <div class="form-row">
                <label for="topic">Topic</label>
                <input type="text" id="topic" name="topic" placeholder="Add a topic" required>
            </div>
            <div class="form-row">
                <label for="date">Date</label>
                <input type="date" id="date" name="date" required>
            </div>
            <div class="form-row">
                <label for="time">Time</label>
                <input type="time" id="time" name="time" required>
            </div>
            <div class="form-row">
                <label for="platform">Platform</label>
                <select name="platform" id="platform" required>
                    <option value="Zoom">Zoom</option>
                    <option value="Google Meet">Google Meet</option>
                    <option value="MS teams">MS teams</option>
                </select>
            </div>
            <div class="form-row">
                <label for="category">Category</label>
                <select name="category_id" id="category" required>
                    ${categoryOptions}
                </select>
            </div>
            <button type="submit">Create Kuppi</button>
        </form>
    `;
    document.getElementById('kuppiModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function openRequestKuppiModal() {
        let categoryOptions = '';
    kuppiCategories.forEach(category => {
        categoryOptions += `<option value="${category.id}">${category.category_name}</option>`;
    });
    document.getElementById('kuppiModalBody').innerHTML = `
        <h2>Request a Kuppi Session</h2>
        <form action="/kuppi/request_kuppi" method="POST">
            <div class="form-row">
                <label for="topic">Topic</label>
                <input type="text" id="topic" name="topic" placeholder="Add a topic" required>
            </div>
            <div class="form-row">
                <label for="category">Category</label>
                <select name="category_id" id="category" required>
                    ${categoryOptions}
                </select>
            </div>
            <button type="submit">Request Kuppi</button>
        </form>
    `;
    document.getElementById('kuppiModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

// Optional: Close modal on overlay click
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('kuppiModal').addEventListener('click', function(e) {
        if (e.target === this) closeKuppiModal();
    });
});

function openKuppiRequestModal(card) {
    const id = card.getAttribute('data-id');
    const topic = card.querySelector('.post-topic')?.innerText || '';
    const category = card.querySelector('.post-category')?.innerText || '';
    const requesterName = card.querySelector('.post-requester-name')?.innerText || '';
    const requesterUniversity = card.querySelector('.post-requester-university')?.innerText || '';

    document.getElementById('kuppiModalBody').innerHTML = `
        <h2>Kuppi Request Details</h2>
        <p><strong>Requester Name:</strong> ${requesterName}</p>
        <p><strong>Requester University:</strong> ${requesterUniversity}</p>
        <p><strong>Category:</strong> ${category}</p>
        <p><strong>Topic:</strong> ${topic}</p>
        <button class="btn" onclick="openVolunteerKuppiModal('${id}', '${topic}', '${category}')">Volunteer to Host</button>
    `;
    document.getElementById('kuppiModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function openVolunteerKuppiModal(id, topic, category) {
    let categoryOptions = '';
    kuppiCategories.forEach(cat => {
        const selected = cat.category_name === category ? 'selected' : '';
        categoryOptions += `<option value="${cat.id}" ${selected}>${cat.category_name}</option>`;
    });

    document.getElementById('kuppiModalBody').innerHTML = `
        <h2>Volunteer to Host Kuppi</h2>
        <form action="/kuppi/approve_request/${id}" method="POST">
            <div class="form-row">
                <label for="topic">Topic</label>
                <input type="text" id="topic" name="topic" value="${topic}" required>
            </div>
            <div class="form-row">
                <label for="date">Date</label>
                <input type="date" id="date" name="date" required>
            </div>
            <div class="form-row">
                <label for="time">Time</label>
                <input type="time" id="time" name="time" required>
            </div>
            <div class="form-row">
                <label for="platform">Platform</label>
                <select name="platform" id="platform" required>
                    <option value="Zoom">Zoom</option>
                    <option value="Google Meet">Google Meet</option>
                    <option value="MS teams">MS teams</option>
                </select>
            </div>
            <div class="form-row">
                <label for="category">Category</label>
                <select name="category_id" id="category" required>
                    ${categoryOptions}
                </select>
            </div>
            <button type="submit">Volunteer</button>
        </form>
    `;
    document.getElementById('kuppiModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function openHostKuppiModalWithPrefill(topic, category) {
    let categoryOptions = '';
    kuppiCategories.forEach(cat => {
        const selected = cat.category_name === category ? 'selected' : '';
        categoryOptions += `<option value="${cat.id}" ${selected}>${cat.category_name}</option>`;
    });

    document.getElementById('kuppiModalBody').innerHTML = `
        <h2>Host a Kuppi Session</h2>
        <form action="/kuppi/create" method="POST">
            <div class="form-row">
                <label for="topic">Topic</label>
                <input type="text" id="topic" name="topic" value="${topic}" required>
            </div>
            <div class="form-row">
                <label for="date">Date</label>
                <input type="date" id="date" name="date" required>
            </div>
            <div class="form-row">
                <label for="time">Time</label>
                <input type="time" id="time" name="time" required>
            </div>
            <div class="form-row">
                <label for="platform">Platform</label>
                <select name="platform" id="platform" required>
                    <option value="Zoom">Zoom</option>
                    <option value="Google Meet">Google Meet</option>
                    <option value="MS teams">MS teams</option>
                </select>
            </div>
            <div class="form-row">
                <label for="category">Category</label>
                <select name="category_id" id="category" required>
                    ${categoryOptions}
                </select>
            </div>
            <button type="submit">Create Kuppi</button>
        </form>
    `;
    document.getElementById('kuppiModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const topic = urlParams.get('topic');
    const category = urlParams.get('category');
    if (topic) document.getElementById('topic').value = topic;
    if (category) document.getElementById('category').value = category;
});
