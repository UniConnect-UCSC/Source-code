let currentEditModal = null;

function toggleOptionsMenu(icon) {
  const menu = icon.parentElement.querySelector(".post-more-options-menu");

  document.querySelectorAll(".post-more-options-menu.show").forEach((m) => {
    if (m !== menu) m.classList.remove("show");
  });

  menu.classList.toggle("show");
}

document.addEventListener("click", (e) => {
  const isMenu = e.target.closest(".post-more-options");
  if (!isMenu) {
    document
      .querySelectorAll(".post-more-options-menu.show")
      .forEach((menu) => menu.classList.remove("show"));
  }
});

function deletePost(btn, event, postType) {
  event.stopPropagation();

  const menu = btn.closest(".post-more-options-menu");
  const postId = menu ? menu.getAttribute("data-post-id") : null;
  if (!postId) return;

  if (confirm("Are you sure you want to delete this post? ")) {
    const formData = new FormData();
    formData.append("delete_post_id", postId);
    if (postType) formData.append("post_type", postType);

    fetch(window.location.pathname, {
      method: "POST",
      body: formData,
    }).then(() => window.location.reload());
  }
}

function resolvePostModalFromTrigger(triggerEl) {
  const postEl = triggerEl ? triggerEl.closest(".post") : null;
  if (!postEl) return null;
  const modal = postEl.nextElementSibling;
  if (!modal || !modal.classList.contains("edit-post-modal")) return null;
  return modal;
}

function openEditPostModal(triggerEl, postImageUrl) {
  const modal = resolvePostModalFromTrigger(triggerEl);
  if (!modal) return;

  currentEditModal = modal;

  const previewImg = modal.querySelector("#edit-preview-img");
  const editPostImage = modal.querySelector(".edit-post-image");

  if (previewImg && editPostImage) {
    if (postImageUrl && postImageUrl !== "" && postImageUrl !== "null") {
      previewImg.src = postImageUrl;
      previewImg.style.display = "block";
      editPostImage.style.display = "block";
    } else {
      previewImg.src = "";
      previewImg.style.display = "none";
      editPostImage.style.display = "none";
    }
  }

  modal.style.zIndex = "1000";
  modal.style.pointerEvents = "auto";
  document.body.style.overflow = "hidden";

  gsap.to(modal, {
    opacity: 1,
    duration: 0.2,
  });
}

function closeEditPostModal() {
  const modal = currentEditModal || document.querySelector(".edit-post-modal");
  if (!modal) return;

  gsap.to(modal, {
    opacity: 0,
    duration: 0.2,
    onComplete: () => {
      modal.style.zIndex = "-1";
      modal.style.pointerEvents = "none";
      document.body.style.overflow = "auto";
      if (currentEditModal === modal) currentEditModal = null;
    },
  });
}

function openEditImageSelector() {
  const modal = currentEditModal;
  if (!modal) return;

  const media = modal.querySelector("#edit-media");
  if (media) media.click();
}

function editPost(postId, postType) {
  const modal = currentEditModal;
  if (!modal) return;

  const caption = modal.querySelector("#edit-post-caption")?.value || "";
  const anonymous = modal.querySelector("#edit-anonymousSwitch input")?.checked
    ? 1
    : 0;
  const mediaInput = modal.querySelector("#edit-media");
  const mediaFile = mediaInput?.files?.[0] || null;

  const formData = new FormData();
  formData.append("edit_post_id", postId);
  formData.append("caption", caption);
  formData.append("is_anonymous", anonymous);
  if (mediaFile) formData.append("media", mediaFile);
  if (postType) formData.append("post_type", postType);

  const spinner = modal.querySelector("#edit-post-loading-spinner");
  if (spinner) spinner.style.display = "flex";

  fetch(window.location.pathname, {
    method: "POST",
    body: formData,
  })
    .then((res) => {
      if (!res.ok) throw new Error("Failed to update post");
      return res.text();
    })
    .then(() => {
      closeEditPostModal();
      window.location.reload();
    })
    .catch((err) => {
      alert("Error updating post: " + err.message);
    })
    .finally(() => {
      if (spinner) spinner.style.display = "none";
    });
}

document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".edit-post-modal").forEach((modal) => {
    const mediaInput = modal.querySelector("#edit-media");
    const previewImg = modal.querySelector("#edit-preview-img");
    const editPostImage = modal.querySelector(".edit-post-image");

    if (mediaInput && previewImg) {
      mediaInput.addEventListener("change", function () {
        const file = mediaInput.files[0];
        if (file) {
          previewImg.src = URL.createObjectURL(file);
          previewImg.style.display = "block";
          if (editPostImage) editPostImage.style.display = "block";
          previewImg.onload = () => URL.revokeObjectURL(previewImg.src);
        }
      });
    }
  });
});
