// Host Kuppi modal
(function(){
  function openHostKuppiModal(kuppiCategories){
    let categoryOptions = '';
    (kuppiCategories||[]).forEach(category => {
      categoryOptions += `<option value="${category.id}">${category.category_name}</option>`;
    });
    const body = document.getElementById('kuppiModalBody');
    if (!body) return;
    body.innerHTML = `
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
          <label for="link">Meeting Link</label>
          <input type="url" id="link" name="link" placeholder="Add meeting link" required>
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
    const overlay = document.getElementById('kuppiModal');
    if (overlay) overlay.style.display = 'flex';
    document.body.style.overflow = 'hidden';
  }
  function openHostKuppiModalWithPrefill(topic, category){
    let categoryOptions = '';
    (window.kuppiCategories||[]).forEach(cat => {
      const selected = cat.category_name === category ? 'selected' : '';
      categoryOptions += `<option value="${cat.id}" ${selected}>${cat.category_name}</option>`;
    });
    const body = document.getElementById('kuppiModalBody');
    if (!body) return;
    body.innerHTML = `
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
    const overlay = document.getElementById('kuppiModal');
    if (overlay) overlay.style.display = 'flex';
    document.body.style.overflow = 'hidden';
  }
  window.openHostKuppiModal = openHostKuppiModal;
  window.openHostKuppiModalWithPrefill = openHostKuppiModalWithPrefill;
})();
