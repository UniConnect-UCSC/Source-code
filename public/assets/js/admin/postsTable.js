function renderPostRow(post) {
  const tr = document.createElement("tr");

  const date = new Date(post.created_at).toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });

  const universityCell = post.university_id
    ? `${post.university_id}`
    : '<span class="type-pill">Global</span>';

  const isReported = Boolean(post.reported);
  const isDeleted = Boolean(post.deleted_at);
  const rowType = post.university_id ? "university" : "global";

  tr.innerHTML = `
    <td>${post.id ?? "-"}</td>
    <td>${post.user_id ?? "-"}</td>
    <td>${universityCell}</td>
    <td class="caption-cell">${post.caption ? escapeHtml(post.caption) : "-"}</td>
    <td>
      <span class="status-pill ${isReported ? "status-yes" : "status-no"}">
        ${isReported ? "Reported" : "Not reported"}
      </span>
    </td>
    <td>
      <button class="table-btn view-btn" onclick="viewPost(${post.id}, '${rowType}')">View</button>
    </td>
    <td>${date}</td>
    <td>
      <button
        class="table-btn delete-btn"
        onclick="softDeletePost(${post.id}, '${rowType}', this)"
        ${isDeleted ? "disabled" : ""}
      >
        ${isDeleted ? "Deleted" : "Delete"}
      </button>
    </td>
  `;

  return tr;
}

function escapeHtml(text) {
  const div = document.createElement("div");
  div.textContent = text ?? "";
  return div.innerHTML;
}

function viewPost(postId, postType) {
  window.open(`/admin/posts/view/${postType}/${postId}`, "_blank");
}

async function softDeletePost(postId, postType, btn) {
  if (!confirm("Are you sure you want to delete this post?")) return;

  btn.disabled = true;
  const originalText = btn.textContent;
  btn.textContent = "Deleting...";

  try {
    const res = await Ajax.jsonPost("/admin/deletePost", {
      post_id: postId,
      post_type: postType,
    });

    if (res?.success) {
      btn.textContent = "Deleted";
      btn.disabled = true;
    } else {
      alert(res?.message || "Failed to delete post.");
      btn.disabled = false;
      btn.textContent = originalText;
    }
  } catch (e) {
    console.error(e);
    alert("An error occurred. Please try again.");
    btn.disabled = false;
    btn.textContent = originalText;
  }
}

function initPostsTable() {
  const body = document.getElementById("posts-table-body");
  const searchInput = document.getElementById("post-search");
  const toggleButtons = document.querySelectorAll(".posts-toggle-btn");

  if (!body) return;

  let currentType = "global";
  const initialRows = body.children.length;

  const postsScroll = new InfinityScroll(
    "getAdminPosts",
    "/admin/postsScrollable",
    body,
    renderPostRow,
    initialRows,
    10,
  );

  postsScroll.setContextProvider(() => ({
    type: currentType,
    search: searchInput?.value.trim() || "",
  }));

  postsScroll.setSkeletonLoader(() => {
    const tr = document.createElement("tr");
    tr.innerHTML = `
      <td colspan="8" style="text-align:center; padding:1rem; color: var(--color-secondary-gray);">
        Loading...
      </td>
    `;
    return tr;
  });

  postsScroll.setupAutoLoadOnScroll();

  const lastRow = body.lastElementChild;
  if (lastRow && postsScroll.observer) {
    postsScroll.observer.observe(lastRow);
    postsScroll.prevObserverElement = lastRow;
  }

  toggleButtons.forEach((btn) => {
    btn.addEventListener("click", () => {
      const nextType = btn.getAttribute("data-type");
      if (!nextType || nextType === currentType) return;

      currentType = nextType;
      toggleButtons.forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");

      postsScroll.offset = 0;
      postsScroll.refresh();
    });
  });

  if (searchInput) {
    let timer = null;
    searchInput.addEventListener("input", () => {
      clearTimeout(timer);
      timer = setTimeout(() => {
        postsScroll.offset = 0;
        postsScroll.refresh();
      }, 350);
    });
  }
}

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initPostsTable);
} else {
  initPostsTable();
}
