(function () {

  /* ── Utility ─────────────────────────────────────────── */

  function escapeHtml(s) {
    if (!s) return '';
    return String(s)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  function el(tag, className, textContent) {
    var node = document.createElement(tag);
    if (className) node.className = className;
    if (textContent) node.textContent = textContent;
    return node;
  }

  /* ══════════════════════════════════════════════════════
     Base KuppiCard class
     ══════════════════════════════════════════════════════ */

  class KuppiCard {
    constructor(item, context) {
      this.item = item || {};
      this.context = context || null; // e.g. 'main', 'my-kuppi', 'my-requests'
    }

    /* ── Shared helpers ──────────────────────────────── */

    getImageSrc() {
      var raw = String(this.item.image_url || '').trim();
      if (!raw) return '/assets/images/ml-banner.jpg';

      if (/^https?:\/\//i.test(raw) || raw.startsWith('//')) {
        return raw;
      }

      return '/' + raw.replace(/^\/+/, '');
    }

    getAltText() {
      return escapeHtml(this.item.topic || 'Kuppi');
    }

    getUniversity() {
      return this.item.university || this.item.requester_university || '';
    }

    getRequesterName() {
      var first = this.item.requester_f_name || '';
      var last  = this.item.requester_l_name || '';
      return (first + ' ' + last).trim();
    }

    /* ── Shared builders ─────────────────────────────── */

    buildImage(className) {
      var wrap = el('div', className);
      var img  = el('img');
      img.src  = this.getImageSrc();
      img.alt  = this.getAltText();
      img.onerror = function () {
        if (img.src.indexOf('/assets/images/ml-banner.jpg') === -1) {
          img.src = '/assets/images/ml-banner.jpg';
        }
      };
      wrap.appendChild(img);
      return wrap;
    }

    setDataAttributes(card) {
      var item = this.item;
      if (item.kuppi_url || item.link)  card.dataset.link     = item.kuppi_url || item.link;
      if (item.host_name)               card.dataset.hostName  = item.host_name;
      if (item.topic)                   card.dataset.topic     = item.topic;
      if (item.category)                card.dataset.category  = item.category;

      var requester = this.getRequesterName();
      if (requester) card.dataset.requesterName = requester;
    }

    /* ── Abstract — subclasses must implement ─────────── */

    render() {
      throw new Error('KuppiCard.render() must be implemented by subclass');
    }
  }

  /* ══════════════════════════════════════════════════════
     MainKuppiCard — main feed cards
     ══════════════════════════════════════════════════════ */

  class MainKuppiCard extends KuppiCard {

    buildHeader() {
      var header     = el('div', 'kuppi-header');
      var headerLeft = el('div');

      if (this.item.topic) {
        headerLeft.appendChild(el('h3', 'kuppi-title', this.item.topic));
      }

      var uni = this.getUniversity();
      if (uni) {
        headerLeft.appendChild(el('span', 'kuppi-university', uni));
      }

      header.appendChild(headerLeft);
      header.appendChild(el('div', 'kuppi-actions'));
      return header;
    }

    buildContentFields(content) {
      if (this.item.kuppi_date_time) {
        content.appendChild(el('div', 'kuppi-date', this.item.kuppi_date_time));
      }
      if (this.item.status) {
        content.appendChild(el('p', 'post-status', this.item.status));
      }
      if (this.item.platform) {
        content.appendChild(el('div', 'kuppi-location', 'Platform: ' + this.item.platform));
      }

      var desc = '';
      if (this.item.host_name) {
        desc = 'Hosted by ' + this.item.host_name;
      } else if (this.item.category) {
        desc = 'Category: ' + this.item.category;
      }
      if (desc) {
        content.appendChild(el('p', 'kuppi-description', desc));
      }
    }

    render() {
      var item = this.item;
      var card = el('div', 'kuppi-card kuppi-post');

      card.onclick = function () {
        if (typeof window.openKuppiModal === 'function') window.openKuppiModal(item);
      };

      card.appendChild(this.buildImage('kuppi-image'));

      var content = el('div', 'kuppi-content');
      content.appendChild(this.buildHeader());
      this.buildContentFields(content);
      this.setDataAttributes(card);
      card.appendChild(content);

      // Inject card-level actions (participate, etc.)
      if (typeof window.renderKuppiCardActions === 'function') {
        try { window.renderKuppiCardActions(card, this.item); }
        catch (e) { console.debug('renderKuppiCardActions failed:', e); }
      }

      return card;
    }
  }

  /* ══════════════════════════════════════════════════════
     ModalKuppiCard — base for all modal card variants
     ══════════════════════════════════════════════════════ */

  class ModalKuppiCard extends KuppiCard {

    buildRibbon() {
      // Treat rows with status 'Requested' as requests; others as hosted
      var isRequest = this.item.status === 'Requested';
      return el('div',
        'kuppi-ribbon ' + (isRequest ? 'kuppi-ribbon--request' : 'kuppi-ribbon--host'),
        isRequest ? 'Request' : 'Host'
      );
    }

    buildHeader(content) {
      if (this.item.topic) {
        content.appendChild(el('h3', 'post-topic', this.item.topic));
      }
      if (this.item.university) {
        content.appendChild(el('p', 'post-university', this.item.university));
      } else if (this.item.requester_university) {
        content.appendChild(el('p', 'post-requester-university', this.item.requester_university));
      }
    }

    buildContentFields(content) {
      if (this.item.category) {
        content.appendChild(el('p', 'post-category', this.item.category));
      }
      if (this.item.host_name) {
        content.appendChild(el('p', 'post-host-name', this.item.host_name));
      }
      if (this.item.kuppi_date_time) {
        content.appendChild(el('p', 'post-datetime', this.item.kuppi_date_time));
      }
      if (this.item.platform) {
        content.appendChild(el('p', 'post-platform', 'Platform: ' + this.item.platform));
      }
      if (this.item.status) {
        content.appendChild(el('p', 'post-status', this.item.status));
      }
      if (this.item.participants) {
        content.appendChild(el('p', 'post-participants', 'Participant Count: ' + this.item.participants));
      }
    }

    /* Subclasses override this to add their specific buttons */
    buildActions() {
      return null;
    }

    render() {
      var item = this.item;
      var card = el('div', 'kuppi-post kuppi-modal-card');

      card.onclick = function (e) {
        // Don't open modal if clicking on action buttons
        if (e.target.closest('.kuppi-post-actions')) return;
        if (typeof window.openKuppiModal === 'function') window.openKuppiModal(item);
      };

      card.appendChild(this.buildRibbon());
      card.appendChild(this.buildImage('post-image'));

      var content = el('div', 'post-content');
      this.buildHeader(content);
      this.buildContentFields(content);
      this.setDataAttributes(card);

      var actions = this.buildActions();
      if (actions) content.appendChild(actions);

      card.appendChild(content);
      return card;
    }
  }

  /* ══════════════════════════════════════════════════════
     MyKuppiCard — "My Kuppis" modal (hosted + requests)
     ══════════════════════════════════════════════════════ */

  class MyHostCard extends ModalKuppiCard {

    buildActions() {
      var item    = this.item;
      var actions = el('div', 'kuppi-post-actions');
      var isCompleted = item.status === 'Completed';

        // "My Hosts" tab: user is host of these sessions, allow host edit + status
      if (!isCompleted) {
        var editBtn = el('button', 'btn btn-primary', 'Edit');
          editBtn.onclick = function (e) {
            e.stopPropagation();
            var dt    = String(item.kuppi_date_time || '');
            var parts = dt.split(' ');
            window.openEditKuppiModal({
              id:       item.id,
              topic:    item.topic    || '',
              date:     parts[0]     || '',
              time:     parts[1]     || '',
              platform: item.platform || '',
              link:     item.kuppi_url || item.link || ''
            });
        };
        actions.appendChild(editBtn);

        var changeStatusBtn = el('button', 'btn btn-primary', 'Change Status');
          changeStatusBtn.onclick = function (e) {
            e.stopPropagation();
            window.openChangeStatusModal({
                item
            });
        };
  
        actions.appendChild(changeStatusBtn);
      }
      

      return actions;
    }
  }

  class MyRequestCard extends ModalKuppiCard {
    buildActions() {
      var item    = this.item;
      var actions = el('div', 'kuppi-post-actions');
      var isApproved = item.host_id ;

      if (!isApproved) {
        var editReqBtn = el('button', 'btn btn-primary', 'Edit');
            editReqBtn.onclick = function (e) {
              e.stopPropagation();
              window.openEditKuppiRequestModal({
                id:       item.id,
                topic:    item.topic    || '',
                category: item.category || ''
              });
            };
            actions.appendChild(editReqBtn);
      }
          
      return actions;
    }
  }

  class MyAttendCard extends ModalKuppiCard {
    buildActions() {
      var item    = this.item;
      var actions = el('div', 'kuppi-post-actions');
      var isCompleted = item.status === 'Completed';

      if (isCompleted) {
        var reviewBtn = el('button', 'btn btn-primary', 'Review');
        reviewBtn.onclick = function (e) {
          e.stopPropagation();
          window.openReviewModal({
            id: item.id,
            host_id :item.host_id
          });
        };
        actions.appendChild(reviewBtn);
      }
      else {
        var removeParticipationBtn = el('button', 'btn btn-primary', 'Remove Participation');
        removeParticipationBtn.onclick = function (e) {
          e.stopPropagation();
          console.log(`Remove Particiapation btn clicked`);
          const kuppiId = item.id;
          const current = true;
          const data = {
            kuppi_id: kuppiId,
            current_status: current,
            action: "participate"
          }
        Ajax.jsonPost('/kuppi/toggle', data).then((response) => {
          if(response.newStatus === !current) {
            console.log(`removed participation successfully`);
            item.is_participating = response.newStatus;
  
              if (window.newMyParticipationsScroll) {
                window.newMyParticipationsScroll.resetScroll();
                window.newMyParticipationsScroll.loadNextElements();
              }
  
          } else {
              console.error('Server response inconsistent with requested toggle action.');
              console.log(response);
          }
  
      });
        }
  
        actions.appendChild(removeParticipationBtn);

          
      }  


      return actions.children.length > 0 ? actions : null;
    }
  }

 

  /* ══════════════════════════════════════════════════════
     KuppiRequestCard — "Kuppi Requests" modal
     ══════════════════════════════════════════════════════ */

  class KuppiRequestCard extends ModalKuppiCard {

    buildActions() {
      var item    = this.item;
      var actions = el('div', 'kuppi-post-actions');

      var volunteerBtn = el('button', 'btn btn-primary', 'Volunteer');
      volunteerBtn.onclick = function (e) {
        e.stopPropagation();
        window.openVolunteerKuppiModal({
          id:           item.id,
          topic:        item.topic || '',
          category:     item.category || ''
        });
      };
      actions.appendChild(volunteerBtn);

      return actions;
    }
  }

    class MyFavoriteCard extends ModalKuppiCard {
    buildActions() {
      var item    = this.item;
      var actions = el('div', 'kuppi-post-actions');
      var isCompleted = item.status === 'Completed';

      if (isCompleted) {
        var reviewBtn = el('button', 'btn btn-primary', 'Review');
        reviewBtn.onclick = function (e) {
          e.stopPropagation();
          window.openReviewModal({
            id: item.id,
            host_id :item.host_id
          });
        };
        actions.appendChild(reviewBtn);
      } else {
        var removeFavoriteBtn = el('button', 'btn btn-primary', 'Remove Favorite');
        removeFavoriteBtn.onclick = function (e) {
          e.stopPropagation();
        const kuppiId = item.id;
        const current = true ;
        const data = {
          kuppi_id: kuppiId,
          current_status: current,
          action: "favorite"
        };

        Ajax.jsonPost('/kuppi/toggle', data).then((response) => {
          if (response.newStatus === !current) {
            console.log(`succefully removed the favorite item`);
            item.is_favorite = response.newStatus;
                if (window.newMyFavoritesScroll) {
                window.newMyFavoritesScroll.resetScroll();
                window.newMyFavoritesScroll.loadNextElements();
              }
          } else {
            console.error('Server response inconsistent with favorite toggle action.');
            console.log(response);
          }
        }).catch((err) => {
          console.error('Favorite toggle failed', err);
        });          
        }
        actions.appendChild(removeFavoriteBtn);
      }

      return actions.children.length > 0 ? actions : null;
    }
  }

  /* ══════════════════════════════════════════════════════
     KuppiCardFactory — creates the right card by context
     ══════════════════════════════════════════════════════ */

  class KuppiCardFactory {
    static create(data, context) {
      switch (context) {
        case 'main':              return new MainKuppiCard(data, context).render();
        case 'my-kuppi':          return new MyHostCard(data, context).render();
        case 'kuppi-requests':    return new KuppiRequestCard(data, context).render();
        case 'my-requests':       return new MyRequestCard(data, context).render();
        case 'my-participations': return new MyAttendCard(data, context).render();
        case 'my-favorites':      return new MyFavoriteCard(data, context).render();
        default:
          console.error('Unknown kuppi card context: ' + context);
          return null;
      }
    }
  }

  /* ── Public API ──────────────────────────────────────── */

  window.renderKuppiCards          = function (data, ctx) { return KuppiCardFactory.create(data, ctx); };
  window.renderMainKuppiCards      = function (data) { return KuppiCardFactory.create(data, 'main'); };
  window.renderMyKuppiCards        = function (data) { return KuppiCardFactory.create(data, 'my-kuppi'); };
  window.renderKuppiRequestsCards  = function (data) { return KuppiCardFactory.create(data, 'kuppi-requests'); };
  window.renderMyRequestsCards     = function (data) { return KuppiCardFactory.create(data, 'my-requests'); };
  window.renderMyParticipationCards= function (data) { return KuppiCardFactory.create(data, 'my-participations'); };
  window.renderMyFavoriteCards     = function (data) { return KuppiCardFactory.create(data, 'my-favorites'); };
  window.renderForYouKuppiCards    = function (data) { return KuppiCardFactory.create(data, 'main'); };

})();
