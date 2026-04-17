(function () {
  if (document.getElementById("comment-panel")) return;

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

  lucide.createIcons();
})();

let activeCommentPostId = null;

function openCommentPanel(postId) {
  activeCommentPostId = postId;

  document.getElementById("comment-panel").classList.add("open");
  document.getElementById("comment-panel-overlay").classList.add("open");
  document.body.style.overflow = "hidden";

  loadComments(postId);
}

function closeCommentPanel() {
  document.getElementById("comment-panel").classList.remove("open");
  document.getElementById("comment-panel-overlay").classList.remove("open");
  document.body.style.overflow = "auto";
  activeCommentPostId = null;
}

async function loadComments(postId) {
  const body = document.getElementById("comment-panel-body");
  body.innerHTML =
    '<p style="color: var(--color-text-secondary); font-size: 14px;">Loading...</p>';

  try {
    const res = await Ajax.request("GET", "/comments/getComments", {
      post_id: postId,
    });
    if (!res.success) throw new Error(res.message);

    const comments = Array.isArray(res.comments) ? res.comments : [];

    if (comments.length === 0) {
      body.innerHTML =
        '<p style="color: var(--color-text-secondary); font-size: 14px;">No comments yet. Be the first!</p>';
      return;
    }

    body.innerHTML = comments
      .map(
        (c) => `
      <div style="margin-bottom: 1rem;">
        <p style="font-weight: 500; font-size: 14px; margin: 0 0 4px;">${c.author ?? "User"}</p>
        <p style="font-size: 14px; margin: 0; color: var(--color-text-secondary);">${c.content ?? c.comment_text ?? ""}</p>
      </div>
    `,
      )
      .join("");
  } catch (e) {
    body.innerHTML =
      '<p style="color: var(--color-text-danger); font-size: 14px;">Failed to load comments.</p>';
    console.error(e);
  }
}
