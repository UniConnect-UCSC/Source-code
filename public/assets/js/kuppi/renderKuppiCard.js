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
    // Use kuppi-specific classes in main feed
    card.className = isMain ? 'kuppi-card kuppi-post' : 'kuppi-post';
    // contextual ribbon for My Kuppis / Requests
    if (isModal) {
      var isRequest = (!item || !item.kuppi_date_time);
      var ribbon = document.createElement('div');
      ribbon.className = 'kuppi-ribbon ' + (isRequest ? 'kuppi-ribbon--request' : 'kuppi-ribbon--host');
      ribbon.textContent = isRequest ? 'Request' : 'Host';
      card.appendChild(ribbon);
    }
    if (isModal) {
      // Use class-based styling for modal cards
      card.classList.add('kuppi-modal-card');
    }
    if (isMain) {
      card.style.cursor = 'pointer';
      card.onclick = function(){ 
        if (typeof window.openKuppiModal === 'function') window.openKuppiModal(card); };
    }

    var imgWrap = document.createElement('div');
    var src = item && item.image_url ? ('/' + String(item.image_url).replace(/^\/+/, '')) : '/assets/images/ml-banner.jpg';
    if (isMain) {
      // Use kuppi-image with img element, styled via CSS
      imgWrap.className = 'kuppi-image';
      var imgMain = document.createElement('img');
      imgMain.src = src;
      imgMain.alt = escapeHtml(item && item.topic ? item.topic : 'Kuppi');
      imgWrap.appendChild(imgMain);
    } else {
      imgWrap.className = 'post-image';
      var img = document.createElement('img');
      img.src = src;
      img.alt = escapeHtml(item && item.topic ? item.topic : 'Kuppi');
      imgWrap.appendChild(img);
    }

    var content = document.createElement('div');
    content.className = isMain ? 'kuppi-content' : 'post-content';

    if (isMain) {
      // Kuppi-style header with title + university badge
      var header = document.createElement('div');
      header.className = 'kuppi-header';
      var headerLeft = document.createElement('div');

      if (item && item.topic){
        var h3 = document.createElement('h3');
        h3.className = 'kuppi-title';
        h3.textContent = item.topic;
        headerLeft.appendChild(h3);
      }
      var uni = item && item.university ? item.university : (item && item.requester_university ? item.requester_university : '');
      if (uni){
        var uniSpan = document.createElement('span');
        uniSpan.className = 'kuppi-university';
        uniSpan.textContent = uni;
        headerLeft.appendChild(uniSpan);
      }

      // No category badge in the header

      header.appendChild(headerLeft);

      // Right actions container (buttons injected by actions module)
      var headerRight = document.createElement('div');
      headerRight.className = 'kuppi-actions';
      header.appendChild(headerRight);

      content.appendChild(header);
    } else {
      // Original compact header for modals
      if (item && item.topic){
        var h3 = document.createElement('h3');
        h3.className = 'post-topic';
        h3.textContent = item.topic;
        content.appendChild(h3);
      }
      if (item && item.university){
        var pUni = document.createElement('p');
        pUni.className = 'post-university';
        pUni.textContent = item.university;
        content.appendChild(pUni);
      } else if (item && item.requester_university){
        var pReqUni = document.createElement('p');
        pReqUni.className = 'post-requester-university';
        pReqUni.textContent = item.requester_university;
        content.appendChild(pReqUni);
      }
    }
    if (!isMain && item && item.category){
      var pCat = document.createElement('p');
      pCat.className = 'post-category';
      pCat.textContent = item.category;
      content.appendChild(pCat);
    }

    if (!isMain && item && item.host_name){
      var pReqName = document.createElement('p');
      pReqName.className = 'post-host-name';
      pReqName.textContent = item.host_name;
      content.appendChild(pReqName);
    }

    var hasDate = item && item.kuppi_date_time;
    if (hasDate){
      if (isMain) {
        var dateDiv = document.createElement('div');
        dateDiv.className = 'kuppi-date';
        dateDiv.textContent = item.kuppi_date_time;
        content.appendChild(dateDiv);
      } else {
        var pDT = document.createElement('p');
        pDT.className = 'post-datetime';
        pDT.textContent = item.kuppi_date_time;
        content.appendChild(pDT);
      }
    }

    if (item && item.platform){
      if (isMain) {
        var locationDiv = document.createElement('div');
        locationDiv.className = 'kuppi-location';
        locationDiv.textContent = 'Platform: ' + item.platform;
        content.appendChild(locationDiv);
      } else {
        var pPlat = document.createElement('p');
        pPlat.className = 'post-platform';
        pPlat.textContent = 'Platform: ' + item.platform;
        content.appendChild(pPlat);
      }
    }

    // In main feed, add a brief description line
    if (isMain) {
      var desc = '';
      if (item && item.host_name) {
        desc = 'Hosted by ' + item.host_name;
      } else if (item && item.category) {
        desc = 'Category: ' + item.category;
      }
      if (desc) {
        var descriptionP = document.createElement('p');
        descriptionP.className = 'kuppi-description';
        descriptionP.textContent = desc;
        content.appendChild(descriptionP);
      }
    }
    // Set data attributes for modal usage (invisible on card)
    // Prefer server-provided kuppi_url, fallback to link
    if (item && (item.kuppi_url || item.link)) {
      card.dataset.link = item.kuppi_url || item.link;
    }
    if (item && item.host_name) {
      card.dataset.hostName = item.host_name;
    }
    // Provide requester name for modal and other handlers
    if (item && (item.requester_f_name || item.requester_l_name)) {
      var _reqFirst = item.requester_f_name || '';
      var _reqLast = item.requester_l_name || '';
      var _requesterName = (_reqFirst + ' ' + _reqLast).trim();
      if (_requesterName) {
        card.dataset.requesterName = _requesterName;
      }
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
    

      var isMyrequest = isMyModal && item && !item.kuppi_date_time;
      // If this is a hosted kuppi (has date/time), use the host edit modal
      if (isMyModal && !isMyrequest) {
        var editBtn = document.createElement('button');
        editBtn.className = 'btn btn-primary';
        editBtn.textContent = 'Edit';
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
        editReqBtn.className = 'btn btn-primary';
        editReqBtn.textContent = 'Edit';
        editReqBtn.onclick = function(e){
          e.stopPropagation();
          var payload = { id: item.id, topic: item.topic || '', category: item.category || '' };
          window.openEditKuppiRequestModal(payload);
        };
        actions.appendChild(editReqBtn);
      }
      if (isRequestModal) {
        var volunteerBtn = document.createElement('button');
        volunteerBtn.className = 'btn btn-primary';
        volunteerBtn.textContent = 'Volunteer';
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
    // Inject actions via shared module
    if (isMain && typeof window.renderKuppiCardActions === 'function') {
      try { window.renderKuppiCardActions(card, item); } catch(e){ console.debug('renderKuppiCardActions failed:', e); }
    }
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
