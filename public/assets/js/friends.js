"use strict";

function ensureFriendRequestsEmptyState() {
  const container = document.querySelector(".friend-requests");
  if (!container) return;

  const cards = container.querySelectorAll(".friend-request-card");
  const empty = container.querySelector(".friend-requests-empty-card");

  if (cards.length === 0 && !empty) {
    const el = document.createElement("div");
    el.className = "friend-requests-empty-card";
    el.innerHTML = `
      <h3 class="friend-requests-empty-title">No pending requests</h3>
      <p class="friend-requests-empty-text">
        You do not have any friend requests right now. New requests will appear here.
      </p>
    `;
    container.appendChild(el);
  }
}

function ensureFriendsEmptyState() {
  const container = document.querySelector(".friends-container");
  if (!container) return;

  const cards = container.querySelectorAll(".friend-card");
  const empty = container.querySelector(".friends-empty-card");

  if (cards.length === 0 && !empty) {
    const el = document.createElement("div");
    el.className = "friends-empty-card";
    el.innerHTML = `
      <h3 class="friends-empty-title">No friends yet</h3>
      <p class="friends-empty-text">
        You do not have any friends at the moment. Start by searching for people and sending friend requests.
      </p>
      <a class="friends-empty-action" href="/search">Find Friends</a>
    `;
    container.appendChild(el);
  }
}

function removeFriendRequestsEmptyState() {
  document.querySelector(".friend-requests-empty-card")?.remove();
}

function removeFriendsEmptyState() {
  document.querySelector(".friends-empty-card")?.remove();
}

function removeRequestCard(btn) {
  btn.closest(".friend-request-card")?.remove();
  ensureFriendRequestsEmptyState();
}

function addFriendCardFromRequest(btn, userId) {
  const requestCard = btn.closest(".friend-request-card");
  const friendsContainer = document.querySelector(".friends-container");
  if (!requestCard || !friendsContainer) return;

  const link = requestCard.querySelector("a");
  const nameEl = requestCard.querySelector(".friend-request-name");
  const uniEl = requestCard.querySelector(".friend-request-university");
  const image = requestCard.querySelector(".friend-request-profile-picture");
  const defaultAvatar = requestCard.querySelector(".default-avatar");

  const href =
    link?.getAttribute("href") || `/users/${encodeURIComponent(userId)}`;
  const name = (nameEl?.textContent || "User").trim();
  const university = (uniEl?.textContent || "University not set").trim();

  const card = document.createElement("div");
  card.className = "friend-card";

  const avatarHtml = image
    ? `<img src="${image.getAttribute("src") || ""}" alt="${image.getAttribute("alt") || name}">`
    : defaultAvatar
      ? defaultAvatar.outerHTML.replace("default-avatar", "friend-avatar")
      : "";

  card.innerHTML = `
    <a href="${href}">
      ${avatarHtml}
      <div class="user-details">
        <h3>${name}</h3>
        <p>${university}</p>
      </div>
    </a>
    <div>
      <button class="unfriend-button" onclick="unfriend(this, '${String(userId).replace(/'/g, "\\'")}')">Unfriend</button>
    </div>
  `;

  removeFriendsEmptyState();
  friendsContainer.appendChild(card);
}

function setUserCardAction(btn, actionHtml) {
  const container = btn.closest(".button-container");
  if (!container) return false;

  const viewProfile = container.querySelector(".view-profile");
  const viewProfileHtml = viewProfile ? viewProfile.outerHTML : "";

  container.innerHTML = `${viewProfileHtml}${actionHtml}`;
  return true;
}

async function sendFriendRequest(btn, userId) {
  const old = btn.textContent;
  btn.disabled = true;
  btn.textContent = "Sending...";

  try {
    const res = await Ajax.jsonPost("/friends/sendFriendRequest", {
      user_id: userId,
    });
    if (res?.success === false) throw new Error(res.message || "Failed");

    btn.textContent = "Cancel Request";
    btn.disabled = false;
    btn.classList.remove("send-friend-request");
    btn.classList.add("pending-friend-request");
    btn.onclick = () => cancelFriendRequest(btn, userId);
  } catch (e) {
    console.error(e);
    btn.textContent = old;
    btn.disabled = false;
  }
}

async function cancelFriendRequest(btn, userId) {
  const old = btn.textContent;
  btn.disabled = true;
  btn.textContent = "Canceling...";

  try {
    const res = await Ajax.jsonPost("/friends/cancelFriendRequest", {
      user_id: userId,
    });
    if (res?.success === false) throw new Error(res.message || "Failed");

    btn.textContent = "Add Friend";
    btn.disabled = false;
    btn.classList.remove("pending-friend-request");
    btn.classList.add("send-friend-request");
    btn.onclick = () => sendFriendRequest(btn, userId);
  } catch (e) {
    console.error(e);
    btn.textContent = old;
    btn.disabled = false;
  }
}

async function acceptFriendRequest(btn, userId) {
  const old = btn.textContent;
  btn.disabled = true;
  btn.textContent = "Accepting...";

  try {
    const res = await Ajax.jsonPost("/friends/acceptFriendRequest", {
      user_id: userId,
    });
    if (res?.success === false) throw new Error(res.message || "Failed");

    // User card context: move to friends state
    const updated = setUserCardAction(
      btn,
      `<button class="friend-button" disabled>Friends</button>`,
    );
    if (updated) return;

    // Friend-requests page context
    const requestCard = btn.closest(".friend-request-card");
    if (requestCard) {
      addFriendCardFromRequest(btn, userId);
      removeRequestCard(btn);
      return;
    }

    // Fallback
    btn.textContent = "Friends";
    btn.disabled = true;
  } catch (e) {
    console.error(e);
    btn.textContent = old;
    btn.disabled = false;
  }
}

async function declineFriendRequest(btn, userId) {
  const old = btn.textContent;
  btn.disabled = true;
  btn.textContent = "Declining...";

  try {
    const res = await Ajax.jsonPost("/friends/cancelFriendRequest", {
      user_id: userId,
    });
    if (res?.success === false) throw new Error(res.message || "Failed");

    const safeUserId = String(userId).replace(/'/g, "\\'");

    // User card context: move to Add Friend
    const updated = setUserCardAction(
      btn,
      `<button class="send-friend-request" onclick="sendFriendRequest(this, '${safeUserId}')">Add Friend</button>`,
    );
    if (updated) return;

    // Friend-requests page context
    removeRequestCard(btn);
  } catch (e) {
    console.error(e);
    btn.textContent = old;
    btn.disabled = false;
  }
}

async function unfriend(btn, userId) {
  const old = btn.textContent;
  btn.disabled = true;
  btn.textContent = "Unfriending...";

  try {
    const res = await Ajax.jsonPost("/friends/unfriend", { user_id: userId });
    if (res?.success === false) throw new Error(res.message || "Failed");

    // Friends-list page: remove card
    const friendCard = btn.closest(".friend-card");
    if (friendCard) {
      friendCard.remove();
      ensureFriendsEmptyState();
      return;
    }

    // Profile page: swap button back to Add Friend without refresh
    btn.textContent = "Add Friend";
    btn.disabled = false;
    btn.onclick = () => sendFriendRequest(btn, userId);

    // Optional: update wrapper class for styling consistency
    const wrapper = btn.closest(".unfriend-actions");
    if (wrapper) {
      wrapper.classList.remove("unfriend-actions");
      wrapper.classList.add("send-friend-request-actions");
    }
  } catch (e) {
    console.error(e);
    btn.textContent = old;
    btn.disabled = false;
  }
}
