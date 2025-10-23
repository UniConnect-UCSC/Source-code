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
  console.log(postType);
  const menu = btn.closest(".post-more-options-menu");
  const postId = menu.getAttribute("data-post-id");
  if (confirm("Are you sure you want to delete this post? ")) {
    const formData = new FormData();
    formData.append("delete_post_id", postId);
    if (postType) {
      formData.append("post_type", postType);
    }
    fetch(window.location.pathname, {
      method: "POST",
      body: formData,
    }).then(() => window.location.reload());
  }
}

function openEditPostModal(postImageUrl) {
  const modal = document.querySelector(".edit-post-modal");

  const previewImg = document.getElementById("edit-preview-img");
  if (previewImg && postImageUrl) {
    previewImg.src = postImageUrl;
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
  const modal = document.querySelector(".edit-post-modal");

  gsap.to(modal, {
    opacity: 0,
    duration: 0.2,
    onComplete: () => {
      modal.style.zIndex = "-1";
      modal.style.pointerEvents = "none";
      document.body.style.overflow = "auto";
    },
  });
}

function openEditImageSelector() {
  const media = document.getElementById("edit-media");
  if (media) {
    media.click();
  }
}

function editPost(postId, postType) {
  const caption = document.getElementById("edit-post-caption")?.value || "";
  const anonymous = document.querySelector("#edit-anonymousSwitch input")
    ?.checked
    ? 1
    : 0;
  const mediaInput = document.getElementById("edit-media");
  const mediaFile = mediaInput?.files?.[0] || null;

  // Build form data for AJAX
  const formData = new FormData();
  formData.append("edit_post_id", postId);
  formData.append("caption", caption);
  formData.append("is_anonymous", anonymous);
  if (mediaFile) {
    formData.append("media", mediaFile);
  }

  if (postType) {
    formData.append("post_type", postType);
  }

  const spinner = document.getElementById("edit-post-loading-spinner");
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
  const mediaInput = document.getElementById("edit-media");
  const previewImg = document.getElementById("edit-preview-img");

  if (mediaInput && previewImg) {
    mediaInput.addEventListener("change", function () {
      const file = mediaInput.files[0];
      if (file) {
        previewImg.src = URL.createObjectURL(file);
        previewImg.onload = () => URL.revokeObjectURL(previewImg.src);
      }
    });
  }
});
