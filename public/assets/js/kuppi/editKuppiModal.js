// Edit Kuppi modal
(function(){
  function openEditKuppiModal(kuppiData){
    if (typeof kuppiData === 'string') {
      kuppiData = JSON.parse(kuppiData);
    }
    const body = document.getElementById('kuppiModalBody');
    if (!body) return;
    body.innerHTML = `
      <h2>Edit Kuppi</h2>
      <form action="/kuppi/edit_kuppi/${kuppiData.id}" method="POST">
        <div class="form-row">
          <label for="topic">Topic</label>
          <input type="text" id="topic" name="topic" value="${kuppiData.topic}" required>
        </div>
        <div class="form-row">
          <label for="date">Date</label>
          <input type="date" id="date" name="date" value="${kuppiData.date}" required>
        </div>
        <div class="form-row">
          <label for="time">Time</label>
          <input type="time" id="time" name="time" value="${kuppiData.time}" required>
        </div>
        <div class="form-row">
          <label for="link">Meeting Link</label>
          <input type="url" id="link" name="link" value="${kuppiData.link || ''}" placeholder="Add meeting link" required>
        </div>
        <div class="form-row">
          <label for="platform">Platform</label>
          <select name="platform" id="platform" required>
            <option value="Zoom" ${kuppiData.platform === 'Zoom' ? 'selected' : ''}>Zoom</option>
            <option value="Google Meet" ${kuppiData.platform === 'Google Meet' ? 'selected' : ''}>Google Meet</option>
            <option value="MS teams" ${kuppiData.platform === 'MS teams' ? 'selected' : ''}>MS teams</option>
          </select>
        </div>
        <button type="submit">Save Changes</button>
      </form>
    `;
    // If MyKuppis overlay is open, hide it before showing the edit modal
    const myOverlay = document.getElementById('myKuppisModal');
    if (myOverlay && myOverlay.style.display !== 'none') {
      // mark that we should restore MyKuppis after closing the edit modal
      window._restoreMyKuppisAfterEdit = true;
      myOverlay.style.display = 'none';
    } else {
      window._restoreMyKuppisAfterEdit = false;
    }

    const overlay = document.getElementById('kuppiModal');
    if (overlay) overlay.style.display = 'flex';
    document.body.style.overflow = 'hidden';
  }
  window.openEditKuppiModal = openEditKuppiModal;
})();
