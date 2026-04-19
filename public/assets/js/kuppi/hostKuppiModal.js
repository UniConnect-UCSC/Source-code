// Host Kuppi modal
(function(){
  function openHostKuppiModal(kuppiCategories){
    // Store categories for later use
    window._hostKuppiCategories = kuppiCategories;
    showLandingView();
  }

  function showLandingView() {
    const body = document.getElementById('kuppiModalBody');
    if (!body) return;
    body.innerHTML = `
      <h2>Host a Kuppi Session</h2>
      <div class="kuppi-slides">
        <div class="kuppi-slide" id="slideHostNew">
          <div class="kuppi-slide-icon">&#128218;</div>
          <h3>Host New Kuppi</h3>
          <p>Create a new peer-led study session on a topic of your choice</p>
        </div>
        <div class="kuppi-slide" id="slideKuppiRequests">
          <div class="kuppi-slide-icon">&#128220;</div>
          <h3>Kuppi Requests</h3>
          <p>Browse topics requested by other students and volunteer to host</p>
        </div>
      </div>
    `;
    const overlay = document.getElementById('kuppiModal');
    if (overlay) overlay.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    attachSlideHandlers();
  }

  function showHostForm(topic, categoryName) {
    const kuppiCategories = window._hostKuppiCategories || window.kuppiCategories || [];
    let categoryOptions = '';
    (kuppiCategories||[]).forEach(category => {
      const selected = (category.category_name === categoryName) ? 'selected' : '';
      categoryOptions += `<option value="${category.id}" ${selected}>${category.category_name}</option>`;
    });
    const body = document.getElementById('kuppiModalBody');
    if (!body) return;
    body.innerHTML = `
      <div class="kuppi-form-header">
        <button class="kuppi-back-btn" id="backToSlides" title="Back">&#8592;</button>
        <h2>Host New Kuppi</h2>
      </div>
      <form action="/kuppi/create" method="POST" enctype="multipart/form-data">
        <div class="form-row">
          <label for="topic">Topic</label>
          <input type="text" id="topic" name="topic" placeholder="Add a topic" value="${topic || ''}" required>
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
        <div class="form-row">
          <label for="kuppi_image">Session Image</label>
          <input type="file" id="kuppi_image" name="kuppi_image" accept="image/*">
        </div>
        <button type="submit">Create Kuppi</button>
      </form>
    `;
    if (window.addDateTimeValidation) window.addDateTimeValidation();
    // Back button
    const backBtn = document.getElementById('backToSlides');
    if (backBtn) {
      backBtn.addEventListener('click', function() {
        showLandingView();
      });
    }
  }

  function attachSlideHandlers() {
    const hostSlide = document.getElementById('slideHostNew');
    const requestsSlide = document.getElementById('slideKuppiRequests');
    if (hostSlide) {
      hostSlide.addEventListener('click', function() {
        showHostForm();
      });
    }
    if (requestsSlide) {
      requestsSlide.addEventListener('click', function() {
        const overlay = document.getElementById('kuppiModal');
        if (overlay) overlay.style.display = 'none';
        document.body.style.overflow = '';
        if (window.openKuppiRequestsModal) {
          window.openKuppiRequestsModal();
        }
      });
    }
  }
  
  function openHostKuppiModalWithPrefill(topic, category){
    window._hostKuppiCategories = window.kuppiCategories || [];
    const body = document.getElementById('kuppiModalBody');
    if (!body) return;
    const overlay = document.getElementById('kuppiModal');
    if (overlay) overlay.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    showHostForm(topic, category);
  }

  window.openHostKuppiModal = openHostKuppiModal;
  window.openHostKuppiModalWithPrefill = openHostKuppiModalWithPrefill;
})();
