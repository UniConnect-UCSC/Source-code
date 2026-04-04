(function () {
  function openChangeStatusModal({ item }) {
    // Remove any existing instance
    const old = document.getElementById('changeStatusModal');
    if (old) old.remove();

    // Overlay using the shared kuppi modal style
    const overlay = document.createElement('div');
    overlay.id = 'changeStatusModal';
    overlay.className = 'kuppi-modal-overlay';

    // Modal shell
    const modal = document.createElement('div');
    modal.className = 'kuppi-modal-content';

    // (Optional) close button in top-right, matching other kuppi modals
    const closeBtn = document.createElement('button');
    closeBtn.type = 'button';
    closeBtn.className = 'kuppi-modal-close';
    closeBtn.innerHTML = '&times;';
    closeBtn.onclick = () => overlay.remove();
    modal.appendChild(closeBtn);

    // Body container styled via #changeStatusModalBody CSS
    const body = document.createElement('div');
    body.id = 'changeStatusModalBody';

    const title = document.createElement('h3');
    title.textContent = 'Change Kuppi Status';
    body.appendChild(title);

    const intro = document.createElement('p');
    intro.textContent = 'Select the new status for this kuppi and confirm your change.';
    body.appendChild(intro);

    const row = document.createElement('div');
    row.className = 'form-row';

    const label = document.createElement('label');
    label.textContent = 'Status';
    row.appendChild(label);

    const select = document.createElement('select');
    ['In Progress', 'Completed', 'Cancelled'].forEach(newStatus => {
      const opt = document.createElement('option');
      opt.value = newStatus;
      opt.textContent = newStatus;
      if (newStatus === item.status) opt.selected = true;
      select.appendChild(opt);
    });
    row.appendChild(select);
    body.appendChild(row);

    const errorMsg = document.createElement('div');
      errorMsg.className = 'status-error';
      body.appendChild(errorMsg);
    // Button row — reuse kuppi-modal-actions + .btn styles
    const btnRow = document.createElement('div');
    btnRow.className = 'kuppi-modal-actions';

    const cancelBtn = document.createElement('button');
    cancelBtn.type = 'button';
    cancelBtn.className = 'btn';
    cancelBtn.textContent = 'Cancel';
    cancelBtn.onclick = () => overlay.remove();

    const confirmBtn = document.createElement('button');
    confirmBtn.type = 'button';
    confirmBtn.className = 'btn btn-primary';
    confirmBtn.textContent = 'Confirm';
    confirmBtn.onclick = () => {
    
    if (select.value === item.status) {
        errorMsg.textContent = 'Please select a different status';
        return;
      }
      errorMsg.textContent = '';
      confirmBtn.disabled = true;
      confirmBtn.textContent = 'confirming ....';

      const data = {
        id            : item.id,
        currentStatus : item.status,
        newStatus     : select.value,
      };

      Ajax.jsonPost('/kuppi/changeStatus', data).then((response) => {
          if(response.success) {
              item.status = response.newStatus ;
              console.log(`successfully changed status ${item.id}`);
              body.innerHTML = '';
              const successIcon = document.createElement('div');
              successIcon.className = 'status-success';
              successIcon.innerHTML = '&#10003;';
              body.appendChild(successIcon);

              const successMsg = document.createElement('p');
              successMsg.className = 'status-success-text';
              successMsg.textContent = 'Status changed successfully';
              body.appendChild(successMsg);

              setTimeout(() => overlay.remove(), 1500);
          } else {
              errorMsg.textContent = response.message || 'Failed to change Status. Please try again.';
              confirmBtn.disabled = true;
              confirmBtn.textContent = 'Change Status';
              console.log('Error ', response.message);

            }
          })
    };

    btnRow.appendChild(cancelBtn);
    btnRow.appendChild(confirmBtn);
    body.appendChild(btnRow);

    modal.appendChild(body);
    overlay.appendChild(modal);
    document.body.appendChild(overlay);
  }

  window.openChangeStatusModal = openChangeStatusModal;
})();