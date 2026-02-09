// Volunteer Host for Kuppi Request
(function(){
  function openKuppiRequestModal(card){
    const id = card.getAttribute('data-id') || card.dataset.id || '';
    const topic = card.getAttribute('data-topic') || card.dataset.topic || (card.querySelector('.post-topic')?.innerText || '');
    const category = card.getAttribute('data-category') || card.dataset.category || (card.querySelector('.post-category')?.innerText || '');
    const requesterName = card.getAttribute('data-requester-name') || card.dataset.requesterName || (card.querySelector('.post-requester-name')?.innerText || '');
    const requesterUniversity = card.getAttribute('data-university') || card.dataset.university || (card.querySelector('.post-requester-university')?.innerText || '');

    const body = document.getElementById('kuppiModalBody');
    if (!body) return;

    body.innerHTML = `
      <h2>Kuppi Request Details</h2>
      <p><strong>Requester Name:</strong> ${requesterName}</p>
      <p><strong>Requester University:</strong> ${requesterUniversity}</p>
      <p><strong>Category:</strong> ${category}</p>
      <p><strong>Topic:</strong> ${topic}</p>
      <button class="btn btn-volunteer">Volunteer to Host</button>
    `;

    // Attach a safe click handler (avoid inline string interpolation issues)
    const volunteerBtn = body.querySelector('.btn.btn-volunteer');
    if (volunteerBtn) {
      volunteerBtn.addEventListener('click', function(){
        openVolunteerKuppiModal({ id, topic, category });
      });
    }

    const overlay = document.getElementById('kuppiModal');
    if (overlay) overlay.style.display = 'flex';
    document.body.style.overflow = 'hidden';
  }

  function openVolunteerKuppiModal(arg1, topic, category){
    // Support both object payload and positional args
    let id;
    if (arg1 && typeof arg1 === 'object') {
      id = arg1.id || '';
      topic = arg1.topic || '';
      category = arg1.category || '';
    } else {
      id = arg1 || '';
      topic = topic || '';
      category = category || '';
    }

    // Get categories whether defined as const or on window
    const categories = (window.kuppiCategories) ||
                       (typeof kuppiCategories !== 'undefined' ? kuppiCategories : []);

    let categoryOptions = '';
    categories.forEach(function(cat){
      const selected = (cat.category_name === category) ? 'selected' : '';
      categoryOptions += `<option value="${cat.id}" ${selected}>${cat.category_name}</option>`;
    });

    const body = document.getElementById('kuppiModalBody');
    if (!body) return;
    body.innerHTML = `
      <h2>Volunteer to Host Kuppi</h2>
      <form action="/kuppi/approve_request/${id}" method="POST">
        <div class="form-row">
          <label for="topic">Topic</label>
          <input type="text" id="topic" name="topic" value="${topic}" required>
        </div>
        <div class="form-row">
          <label for="date">Date</label>
          <input type="date" id="date" name="date" required>
          <div id="date-error" class="error-message"></div>
        </div>
        <div class="form-row">
          <label for="time">Time</label>
          <input type="time" id="time" name="time" required>
          <div id="time-error" class="error-message"></div>
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
          <label for="link">Meeting Link</label>
          <input type="url" id="link" name="link" placeholder="Add meeting link" required>
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
    const overlay = document.getElementById('kuppiModal');
    if (overlay) overlay.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    if (window.addDateTimeValidation) window.addDateTimeValidation();
  }

  window.openKuppiRequestModal = openKuppiRequestModal;
  window.openVolunteerKuppiModal = openVolunteerKuppiModal;
})();
