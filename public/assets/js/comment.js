(function () {
  document.addEventListener("DOMContentLoaded", ensureCommentPanel);
})();

let activeCommentPostId = null;

function ensureCommentPanel() {
  if (document.getElementById("comment-panel")) return true;

  const hasCommentTrigger = document.querySelector(
    "button[onclick*='openCommentPanel(']",
  );
  if (!hasCommentTrigger) return false;

  document.body.insertAdjacentHTML(
    "beforeend",
    `
      <div class="comment-panel-overlay" id="comment-panel-overlay" onclick="closeCommentPanel()"></div>
      <div class="comment-panel" id="comment-panel">
        <div class="comment-panel-header">
          <span>Comments</span>
          <button onclick="closeCommentPanel()">
            <i data-lucide="x"></i>
          </button>
        </div>
        <div class="comment-panel-body" id="comment-panel-body">
          <p style="color: var(--color-text-secondary); font-size: 14px;">Loading...</p>
        </div>
        <div class="comment-panel-footer">
          <textarea id="comment-input" placeholder="Write a comment..." rows="2"></textarea>
          <button onclick="submitComment()">Post</button>
        </div>
      </div>
    `,
  );

  if (window.lucide?.createIcons) {
    window.lucide.createIcons();
  }

  return true;
}

function openCommentPanel(postId) {
  if (!ensureCommentPanel()) return;

  activeCommentPostId = postId;

  const panel = document.getElementById("comment-panel");
  const overlay = document.getElementById("comment-panel-overlay");
  if (!panel || !overlay) return;

  panel.classList.add("open");
  overlay.classList.add("open");
  document.body.style.overflow = "hidden";

  loadComments(postId);
}

function closeCommentPanel() {
  const panel = document.getElementById("comment-panel");
  const overlay = document.getElementById("comment-panel-overlay");

  if (panel) panel.classList.remove("open");
  if (overlay) overlay.classList.remove("open");

  document.body.style.overflow = "auto";
  activeCommentPostId = null;
}

async function loadComments(postId) {
  const body = document.getElementById("comment-panel-body");
  if (!body) return;

  body.innerHTML =
    '<p style="color: var(--color-text-secondary); font-size: 14px;">Loading...</p>';

  try {
    const res = await Ajax.request("GET", "/comments/getComments", {
      post_id: postId,
    });

    if (!res || !res.success) {
      throw new Error(res?.message || "Failed to fetch comments.");
    }

    const comments = Array.isArray(res.comments) ? res.comments : [];

    if (comments.length === 0) {
      body.innerHTML =
        '<p style="color: var(--color-text-secondary); font-size: 14px;">No comments yet. Be the first!</p>';
      return;
    }

    body.innerHTML = comments
      .map((c) => {
        const firstName = (c.f_name || "").trim();
        const lastName = (c.l_name || "").trim();
        const author = (
          c.author ||
          `${firstName} ${lastName}`.trim() ||
          "User"
        ).trim();
        const content = c.content ?? c.comment_text ?? "";
        const profilePicture = c.profile_picture ?? "";

        const initials =
          `${firstName.charAt(0)}${lastName.charAt(0)}`.toUpperCase() ||
          author.charAt(0).toUpperCase() ||
          "U";

        const avatarHtml = profilePicture
          ? `<img class="profile-image" src="${profilePicture}" alt="${author}">`
          : `<span class="profile">${initials}</span>`;

        return `
      <div style="display:flex; gap:10px; margin-bottom:1rem; align-items:flex-start; border-bottom: 1px solid var(--color-primary-gray); padding-bottom: 1rem;">
        <div class="profile-section" style="flex-shrink:0;">${avatarHtml}</div>
        <div>
          <p style="font-weight:500; font-size:14px; margin:0 0 4px;">${author}</p>
          <p style="font-size:14px; margin:0; color:var(--color-text-secondary);">${content}</p>
        </div>
      </div>
    `;
      })
      .join("");
  } catch (e) {
    body.innerHTML =
      '<p style="color: var(--color-text-danger); font-size: 14px;">Failed to load comments.</p>';
    console.error(e);
  }
}

async function submitComment() {
  const input = document.getElementById("comment-input");
  if (!input) return;

  const commentText = input.value.trim();
  if (commentText === "") return;

  try {
    const res = await Ajax.request("POST", "/comments/addComment", {
      post_id: activeCommentPostId,
      comment_text: commentText,
    });

    if (!res || !res.success) {
      throw new Error(res?.message || "Failed to submit comment.");
    }

    input.value = "";
    loadComments(activeCommentPostId);
  } catch (e) {
    alert(e.message || "Failed to submit comment.");
    console.error(e);
  }
}
