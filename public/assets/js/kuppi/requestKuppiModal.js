// Request Kuppi modal
(function(){
  function openRequestKuppiModal(kuppiCategories){
    let categoryOptions = '';
    (kuppiCategories||[]).forEach(category => {
      categoryOptions += `<option value="${category.id}">${category.category_name}</option>`;
    });
    const body = document.getElementById('kuppiModalBody');
    if (!body) return;
    body.innerHTML = `
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
    const overlay = document.getElementById('kuppiModal');
    if (overlay) overlay.style.display = 'flex';
    document.body.style.overflow = 'hidden';
  }
  window.openRequestKuppiModal = openRequestKuppiModal;
})();
