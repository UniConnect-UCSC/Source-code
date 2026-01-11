//const { act } = require("react");

(function(){
  function escapeHtml(s){
    if (!s) return '';
    return String(s)
    .replace(/&/g,'&amp;')
    .replace(/</g,'&lt;')
    .replace(/>/g,'&gt;')
    .replace(/"/g,'&quot;')
    .replace(/'/g,'&#39;');
  }
  
  function buildKuppiCard(item, context){
    var isMyModal = (context === 'my-kuppi-modal');
    var isRequestModal = (context === 'kuppi-requests-modal');
    var isModal = (context === 'kuppi-requests-modal' || context === 'my-kuppi-modal');
    var isMain = (context === 'main');
    var card = document.createElement('div');
    card.className = 'kuppi-post';
    // contextual ribbon for My Kuppis / Requests
    if (isModal) {
      var isRequest = (!item || !item.kuppi_date_time);
      var ribbon = document.createElement('div');
      ribbon.className = 'kuppi-ribbon ' + (isRequest ? 'kuppi-ribbon--request' : 'kuppi-ribbon--host');
      ribbon.textContent = isRequest ? 'Request' : 'Host';
      card.appendChild(ribbon);
    }
    if (isModal) {
      // Compact styling inline to avoid CSS file edits
      card.style.display = 'grid';
      card.style.gridTemplateColumns = '96px 1fr';
      card.style.gap = '12px';
      card.style.alignItems = 'start';
      card.style.border = '1px solid #e0e0ff';
      card.style.borderRadius = '8px';
      card.style.padding = '10px';
      card.style.background = '#fff';
    }
    if (isMain) {
      card.style.cursor = 'pointer';
      card.onclick = function(){ 
        if (typeof window.openKuppiModal === 'function') window.openKuppiModal(card); };
    }

    var imgWrap = document.createElement('div');
    imgWrap.className = 'post-image';
    var img = document.createElement('img');
    var src = item && item.image_url ? ('/' + String(item.image_url).replace(/^\/+/, '')) : '/assets/images/ml-banner.jpg';
    img.src = src;
    img.alt = escapeHtml(item && item.topic ? item.topic : 'Kuppi');
    if (isModal) {
      img.style.width = '96px';
      img.style.height = '72px';
      img.style.objectFit = 'cover';
      img.style.borderRadius = '6px';
    }
    imgWrap.appendChild(img);

    var content = document.createElement('div');
    content.className = 'post-content';
    if (isModal) {
      content.style.display = 'block';
    }

    if (item && item.topic){
      var h3 = document.createElement('h3');
      h3.className = 'post-topic';
      h3.textContent = item.topic;
      if (isModal) {
        h3.style.fontSize = '1rem';
        h3.style.margin = '0 0 6px 0';
      }
      content.appendChild(h3);
    }
    if (item && item.university){
      var pUni = document.createElement('p');
      pUni.className = 'post-university';
      pUni.textContent = item.university;
      if (isModal) {
        pUni.style.margin = '2px 0';
        pUni.style.fontSize = '0.85rem';
        pUni.style.color = '#333';
      }
      content.appendChild(pUni);
    } else if (item && item.requester_university){
      var pReqUni = document.createElement('p');
      pReqUni.className = 'post-requester-university';
      pReqUni.textContent = item.requester_university;
      if (isModal) {
        pReqUni.style.margin = '2px 0';
        pReqUni.style.fontSize = '0.85rem';
        pReqUni.style.color = '#333';
      }
      content.appendChild(pReqUni);
    }
    if (item && item.category){
      var pCat = document.createElement('p');
      pCat.className = 'post-category';
      pCat.textContent = item.category;
      if (isModal) {
        pCat.style.margin = '2px 0';
        pCat.style.fontSize = '0.85rem';
        pCat.style.color = '#333';
      }
      content.appendChild(pCat);
    }

    if (item && item.host_name){
      var pReqName = document.createElement('p');
      pReqName.className = 'post-host-name';
      pReqName.textContent = item.host_name;
      if (isModal) {
        pReqName.style.margin = '2px 0';
        pReqName.style.fontSize = '0.85rem';
        pReqName.style.color = '#333';
      }
      content.appendChild(pReqName);
    }

    var hasDate = item && item.kuppi_date_time;
    if (hasDate){
      var pDT = document.createElement('p');
      pDT.className = 'post-datetime';
      // Keep formatting simple; server may already format in view; here we display raw or split if needed
      pDT.textContent = item.kuppi_date_time;
      if (isModal) {
        pDT.style.margin = '2px 0';
        pDT.style.fontSize = '0.85rem';
        pDT.style.color = '#333';
      }
      content.appendChild(pDT);
    }

    if (item && item.platform){
      var pPlat = document.createElement('p');
      pPlat.className = 'post-platform';
      pPlat.textContent = 'Platform: ' + item.platform;
      if (isModal) {
        pPlat.style.margin = '2px 0';
        pPlat.style.fontSize = '0.85rem';
        pPlat.style.color = '#333';
      }
      content.appendChild(pPlat);
    }
    // Set data attributes for modal usage (invisible on card)
    // Prefer server-provided kuppi_url, fallback to link
    if (item && (item.kuppi_url || item.link)) {
      card.dataset.link = item.kuppi_url || item.link;
    }
    if (item && item.host_name) {
      card.dataset.hostName = item.host_name;
    }
    // Add topic/category to dataset for search/filter usage
    if (item && item.topic) {
      card.dataset.topic = item.topic;
    }
    if (item && item.category) {
      card.dataset.category = item.category;
    }


    // Actions in modal context
    if (isModal) {
      var actions = document.createElement('div');
      actions.className = 'kuppi-post-actions';
      actions.style.marginTop = '8px';
      actions.style.display = 'flex';
      actions.style.gap = '8px';
    

      var isMyrequest = isMyModal && item && !item.kuppi_date_time;
      // If this is a hosted kuppi (has date/time), use the host edit modal
      if (isMyModal && !isMyrequest) {
        var editBtn = document.createElement('button');
        editBtn.className = 'btn btn-edit';
        editBtn.textContent = 'Edit';
        editBtn.style.background = '#6c63ff';
        editBtn.style.color = '#fff';
        editBtn.style.border = 'none';
        editBtn.style.borderRadius = '6px';
        editBtn.style.padding = '6px 12px';
        editBtn.style.fontSize = '0.85rem';
        editBtn.style.cursor = 'pointer';
        editBtn.onclick = function(e){
          e.stopPropagation();
          var dt = String(item.kuppi_date_time || '');
          var parts = dt.split(' ');
          var date = parts[0] || '';
          var time = parts[1] || '';
          var payload = { id: item.id, topic: item.topic || '', date: date, time: time, platform: item.platform || '' };
          window.openEditKuppiModal(payload);
        };
        actions.appendChild(editBtn);
      }
      // If this looks like a request (has requester fields, no date/time), use the request edit modal
      if (isMyrequest) {
        var editReqBtn = document.createElement('button');
        editReqBtn.className = 'btn btn-edit';
        editReqBtn.textContent = 'Edit';
        editReqBtn.style.background = '#6c63ff';
        editReqBtn.style.color = '#fff';
        editReqBtn.style.border = 'none';
        editReqBtn.style.borderRadius = '6px';
        editReqBtn.style.padding = '6px 12px';
        editReqBtn.style.fontSize = '0.85rem';
        editReqBtn.style.cursor = 'pointer';
        editReqBtn.onclick = function(e){
          e.stopPropagation();
          var payload = { id: item.id, topic: item.topic || '', category: item.category || '' };
          window.openEditKuppiRequestModal(payload);
        };
        actions.appendChild(editReqBtn);
      }
      if (isRequestModal) {
        var volunteerBtn = document.createElement('button');
        volunteerBtn.className = 'btn btn-volunteer';
        volunteerBtn.textContent = 'Volunteer';
        volunteerBtn.style.background = '#6c63ff';
        volunteerBtn.style.color = '#fff';
        volunteerBtn.style.border = 'none';
        volunteerBtn.style.borderRadius = '6px';
        volunteerBtn.style.padding = '6px 12px';
        volunteerBtn.style.fontSize = '0.85rem';
        volunteerBtn.style.cursor = 'pointer';
        volunteerBtn.onclick = function(e){
          e.stopPropagation();
          var payload = { id: item.id, topic: item.topic || '',category: item.category || '' };
          window.openVolunteerKuppiModal(payload);
        }
        actions.appendChild(volunteerBtn);
      }

      if (actions.children.length > 0) {
        content.appendChild(actions);
      }
    }

    card.appendChild(imgWrap);
    card.appendChild(content);
    return card;
  }

  function renderMainKuppiCards(data){
    return buildKuppiCard(data, 'main');
  }

  function renderMyKuppiCards(data){
    return buildKuppiCard(data, 'my-kuppi-modal');
  }

  function renderKuppiRequestsCards(data){
    return buildKuppiCard(data, 'kuppi-requests-modal');
  }

  function renderMyRequestsCards(data) {
    return buildKuppiCard(data, 'my-kuppi-modal');
  }
  window.renderMainKuppiCards = renderMainKuppiCards;
  window.renderMyKuppiCards = renderMyKuppiCards;
  window.renderKuppiRequestsCards = renderKuppiRequestsCards;
  window.renderMyRequestsCards = renderMyRequestsCards;
})();
