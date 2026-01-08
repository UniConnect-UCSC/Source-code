// Edit Kuppi Request modal
(function(){
  function openEditKuppiRequestModal(kuppiData){
    if (typeof kuppiData === 'string') {
      try { kuppiData = JSON.parse(kuppiData); } catch(e) { kuppiData = {}; }
    }
    const id = kuppiData.id || '';
    const topic = kuppiData.topic || '';
    const category = kuppiData.category || '';
    let categoryOptions = '';
    const categoriesSrc = Array.isArray(typeof kuppiCategories !== 'undefined' ? kuppiCategories : window.kuppiCategories)
        ? (typeof kuppiCategories !== 'undefined' ? kuppiCategories : window.kuppiCategories)
        : [];
    categoriesSrc.forEach(function(cat){
      const isSelected = String(cat.category_name) === String(category) || String(cat.id) === String(category);
      categoryOptions += `<option value="${cat.id}" ${isSelected ? 'selected' : ''}>${cat.category_name}</option>`;
    });

    const body = document.getElementById('kuppiModalBody');
    if (!body) return;
    body.innerHTML = `
      <h2>Edit Kuppi Request</h2>
      <form action="/kuppi/editKuppiRequest/${id}" method="POST">
        <div class="form-row">
          <label for="topic">Topic</label>
          <input type="text" id="topic" name="topic" value="${topic}" required>
        </div>
        <div class="form-row">
          <label for="category">Category</label>
          <select name="category_id" id="category" required>
            ${categoryOptions}
          </select>
        </div>
        <button type="submit">Save Changes</button>
      </form>
    `;
    // If MyKuppis overlay is open, hide it before showing the edit modal
    const myOverlay = document.getElementById('myKuppisModal');
    if (myOverlay && myOverlay.style.display !== 'none') {
      window._restoreMyKuppisAfterEdit = true;
      myOverlay.style.display = 'none';
    } else {
      window._restoreMyKuppisAfterEdit = false;
    }

    const overlay = document.getElementById('kuppiModal');
    if (overlay) overlay.style.display = 'flex';
    document.body.style.overflow = 'hidden';
  }
  window.openEditKuppiRequestModal = openEditKuppiRequestModal;
})();
