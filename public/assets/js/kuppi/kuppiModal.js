// Detail Kuppi modal: open + close
(function(){
  function openKuppiModal(itemOrCard) {
    var item = itemOrCard;

    // Legacy support: if a DOM element is passed, scrape data from it
    if (itemOrCard instanceof HTMLElement) {
      item = {
        topic:       itemOrCard.querySelector('.kuppi-title')?.innerText || itemOrCard.querySelector('.post-topic')?.innerText || '',
        university:  itemOrCard.querySelector('.kuppi-university')?.innerText || itemOrCard.querySelector('.post-university')?.innerText || '',
        kuppi_date_time: itemOrCard.querySelector('.kuppi-date')?.innerText || itemOrCard.querySelector('.post-datetime')?.innerText || '',
        platform:    (itemOrCard.querySelector('.kuppi-location')?.innerText || itemOrCard.querySelector('.post-platform')?.innerText || '').replace(/^Platform:\s*/i, ''),
        kuppi_url:   itemOrCard.dataset?.link || '',
        host_name:   itemOrCard.dataset?.hostName || '',
        requester_name: itemOrCard.getAttribute('data-requester-name') || itemOrCard.dataset?.requesterName || '',
        image_url:   itemOrCard.querySelector('.kuppi-image img')?.src || itemOrCard.querySelector('.post-image img')?.src || '',
        category:    itemOrCard.dataset?.category || itemOrCard.querySelector('.post-category')?.innerText || '',
        status:      itemOrCard.querySelector('.post-status')?.innerText || '',
        host_id:     itemOrCard.dataset?.hostId || '',
        id:          itemOrCard.dataset?.kuppiId || ''
      };
    }

    var topic    = item.topic || '';
    var university = item.university || item.requester_university || '';
    var datetime = item.kuppi_date_time || '';
    var date = '', time = '';
    if (datetime && datetime.includes(' ')) {
      var parts = datetime.split(' ');
      date = parts[0] || '';
      time = parts[1] || '';
    }
    var platform = (item.platform || '').replace(/^Platform:\s*/i, '');
    var link     = item.kuppi_url || item.link || '';
    if (link && !/^https?:\/\//i.test(link)) link = 'https://' + link;
    var hostName     = item.host_name || '';
    var requesterName = item.requester_name || item.requester_f_name ? ((item.requester_f_name || '') + ' ' + (item.requester_l_name || '')).trim() : '';
    var image    = item.image_url ? (item.image_url.startsWith('http') ? item.image_url : '/' + item.image_url.replace(/^\/+/, '')) : '/assets/images/ml-banner.jpg';
    var category = item.category || '';
    var status   = item.status || '';
    var isCompleted = (status === 'Completed');

    var body = document.getElementById('kuppiModalBody');
    if (!body) return;
    body.innerHTML = '\n' +
      '<h2>' + topic + '</h2>\n' +
      '<div class="kuppi-modal-layout">\n' +
        '<div class="kuppi-modal-image"><img src="' + image + '" alt="' + topic + '"></div>' +
        '<div class="kuppi-modal-side">\n' +
          '<div class="kuppi-details kuppi-details-compact">\n' +
            (requesterName ? '<div class="kuppi-detail-value">Requested by <strong>' + requesterName + '</strong></div>' : '') +
            (hostName ? '<div class="kuppi-detail-value kuppi-host-row"><span>Hosted by <strong>' + hostName + '</strong></span></div>' : '') +
            (university ? '<div class="kuppi-detail-value"><span class="kuppi-university">' + university + '</span></div>' : '') +
            ((date || time) ? '<div class="kuppi-detail-value kuppi-meta">' + date + ' ' + time + '</div>' : '') +
            (category ? '<div class="kuppi-detail-value">Category: ' + category + '</div>' : '') +
            (platform ? '<div class="kuppi-detail-value kuppi-meta">Platform: ' + platform + '</div>' : '') +
            (status ? '<div class="kuppi-detail-value">Status: <strong>' + status + '</strong></div>' : '') +
            '<div class="kuppi-detail-value kuppi-link-row">' + (link ? '<a href="' + link + '" target="_blank" rel="noopener noreferrer">' + link + '</a>' : '<span style="color:#9ca3af">Not provided</span>') + '</div>\n' +
          '</div>\n' +
        '</div>\n' +
      '</div>\n';

    // Add Reviews button for completed kuppis
    if (isCompleted) {
      var reviewsSection = document.createElement('div');
      reviewsSection.className = 'kuppi-modal-reviews-section';

      var reviewsBtn = document.createElement('button');
      reviewsBtn.className = 'btn btn-primary kuppi-reviews-btn';
      reviewsBtn.textContent = 'Reviews';
      reviewsBtn.onclick = function () {
        loadReviews(item, reviewsSection);
      };
      reviewsSection.appendChild(reviewsBtn);
      body.appendChild(reviewsSection);
    }

    var modal = document.getElementById('kuppiModal');
    if (modal) modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
  }

  function loadReviews(item, container) {
    // Replace button with loading state
    container.innerHTML = '<p class="reviews-loading">Loading reviews...</p>';

    var kuppiId = item.id || '';

    Ajax.jsonPost('/kuppi/getReviews', { kuppi_id: kuppiId })
      .then(function (response) {
        container.innerHTML = '';

        var heading = document.createElement('h3');
        heading.className = 'reviews-heading';
        heading.textContent = 'Reviews';
        container.appendChild(heading);

        if (!response.success || !response.reviews || response.reviews.length === 0) {
          var noReviews = document.createElement('p');
          noReviews.className = 'reviews-empty';
          noReviews.textContent = 'No reviews yet for this kuppi.';
          container.appendChild(noReviews);
          return;
        }

        var list = document.createElement('div');
        list.className = 'reviews-list';

        response.reviews.forEach(function (review) {
          var card = document.createElement('div');
          card.className = 'review-card';

          var header = document.createElement('div');
          header.className = 'review-card-header';

          var name = document.createElement('span');
          name.className = 'review-card-name';
          name.textContent = review.reviewer_name || 'Anonymous';

          var stars = document.createElement('span');
          stars.className = 'review-card-stars';
          var rating = parseInt(review.rating) || 0;
          for (var i = 1; i <= 5; i++) {
            var star = document.createElement('span');
            star.className = 'review-star-display' + (i <= rating ? ' filled' : '');
            star.innerHTML = '&#9733;';
            stars.appendChild(star);
          }

          header.appendChild(name);
          header.appendChild(stars);
          card.appendChild(header);

          if (review.review_text) {
            var text = document.createElement('p');
            text.className = 'review-card-text';
            text.textContent = review.review_text;
            card.appendChild(text);
          }

          if (review.created_at) {
            var date = document.createElement('span');
            date.className = 'review-card-date';
            date.textContent = review.created_at;
            card.appendChild(date);
          }

          list.appendChild(card);
        });

        container.appendChild(list);
      })
      .catch(function () {
        container.innerHTML = '<p class="reviews-error">Failed to load reviews. Please try again.</p>';
      });
  }

  function closeKuppiModal(){
    const overlay = document.getElementById('kuppiModal');
    if (overlay) overlay.style.display = 'none';
    document.body.style.overflow = '';
    // Restore My Kuppis overlay if it was previously open
    if (window._restoreMyKuppisAfterEdit) {
      const myOverlay = document.getElementById('myKuppisModal');
      if (myOverlay) {
        myOverlay.style.display = 'flex';
      }
      window._restoreMyKuppisAfterEdit = false;
    }
  }

  window.openKuppiModal = openKuppiModal;
  window.closeKuppiModal = closeKuppiModal;
})();
