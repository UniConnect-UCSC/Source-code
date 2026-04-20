function renderUserRow(user) {
  const tr = document.createElement("tr");

  const avatarHtml = user.profile_picture
    ? `<img class="user-avatar" src="${user.profile_picture}" alt="avatar">`
    : `<div class="user-avatar-initials">${(user.f_name?.[0] ?? "").toUpperCase()}${(user.l_name?.[0] ?? "").toUpperCase()}</div>`;

  const date = new Date(user.account_created_at).toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });

  const isBanned = Boolean(user.deleted_at);
  const banLabel = isBanned ? "Unban" : "Ban";

  tr.innerHTML = `
    <td>
      <div class="user-cell">
        ${avatarHtml}
        <span>${user.f_name} ${user.l_name}</span>
      </div>
    </td>
    <td>${user.email}</td>
    <td>${date}</td>
    <td>
      <button class="table-btn view-btn" onclick="viewUser('${user.id}')">View</button>
      <button class="table-btn ban-btn" onclick="toggleBanStatus('${user.id}', ${isBanned}, this)">${banLabel}</button>
      
    </td>
  `;

  return tr;
}

function viewUser(userId) {
  window.open(`/users/${userId}`, "_blank");
}

// Backward compatibility if older markup still calls banUser(userId)
function banUser(userId, btn = null) {
  return toggleBanStatus(userId, false, btn);
}

async function toggleBanStatus(userId, isBanned, btn) {
  // Handle bool passed as string from inline onclick
  if (typeof isBanned === "string") {
    isBanned = isBanned === "true";
  }

  const action = isBanned ? "unban" : "ban";
  if (!confirm(`Are you sure you want to ${action} this user?`)) return;

  const button = btn || null;
  const originalText = button?.textContent || (isBanned ? "Unban" : "Ban");

  if (button) {
    button.disabled = true;
    button.textContent = isBanned ? "Unbanning..." : "Banning...";
  }

  try {
    const url = isBanned ? "/admin/unbanUser" : "/admin/banUser";
    const res = await Ajax.jsonPost(url, { user_id: userId });

    if (res?.success) {
      const nowBanned = !isBanned;

      if (button) {
        button.disabled = false;
        button.textContent = nowBanned ? "Unban" : "Ban";
        button.setAttribute(
          "onclick",
          `toggleBanStatus('${userId}', ${nowBanned}, this)`,
        );
      }
    } else {
      alert(res?.message || `Failed to ${action} user.`);
      if (button) {
        button.disabled = false;
        button.textContent = originalText;
      }
    }
  } catch (e) {
    console.error(e);
    alert("An error occurred. Please try again.");
    if (button) {
      button.disabled = false;
      button.textContent = originalText;
    }
  }
}

// async function deleteUser(userId, btn) {
//   if (
//     !confirm(
//       "Are you sure you want to delete this user? This action cannot be undone.",
//     )
//   ) {
//     return;
//   }

//   btn.disabled = true;
//   btn.textContent = "Deleting...";

//   try {
//     const res = await Ajax.jsonPost("/admin/deleteUser", { user_id: userId });

//     if (res?.success) {
//       btn.closest("tr")?.remove();
//     } else {
//       alert(res?.message || "Failed to delete user.");
//       btn.disabled = false;
//       btn.textContent = "Delete";
//     }
//   } catch (e) {
//     console.error(e);
//     alert("An error occurred. Please try again.");
//     btn.disabled = false;
//     btn.textContent = "Delete";
//   }
// }

function initUsersTable() {
  const usersTableBody = document.getElementById("users-table-body");
  const searchInput = document.getElementById("user-search");

  if (!usersTableBody) return;

  const usersScroll = new InfinityScroll(
    "getAdminUsers",
    "/admin/usersScrollable",
    usersTableBody,
    renderUserRow,
    15,
    10,
  );

  usersScroll.setSkeletonLoader(() => {
    const tr = document.createElement("tr");
    tr.className = "skeleton-row";
    tr.innerHTML = `
      <td colspan="4" style="text-align: center; padding: 1rem; color: var(--color-secondary-gray); font-size: var(--text-sm);">
        Loading...
      </td>
    `;
    return tr;
  });

  usersScroll.setupAutoLoadOnScroll();

  const lastRow = usersTableBody.lastElementChild;
  if (lastRow && usersScroll.observer) {
    usersScroll.observer.observe(lastRow);
    usersScroll.prevObserverElement = lastRow;
  }

  if (searchInput) {
    let searchTimeout = null;
    searchInput.addEventListener("input", function () {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        usersScroll.refresh({ search: searchInput.value.trim() });
      }, 400);
    });
  }
}

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initUsersTable);
} else {
  initUsersTable();
}
