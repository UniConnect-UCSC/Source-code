function openCreatePostModal() {
  const modal = document.getElementById("create-post-modal");
  modal.style.zIndex = "1000";
  document.body.style.overflow = "hidden";
  gsap.set(modal, { opacity: 0 });
  gsap.to(modal, {
    duration: 0.3,
    ease: "power2.out",
    opacity: 1,
  });
}

function closeCreatePostModal() {
  const modal = document.getElementById("create-post-modal");

  // animate first, then hide and restore scroll
  gsap.to(modal, {
    duration: 0.25,
    ease: "power2.in",
    opacity: 0,
    onComplete: () => {
      modal.style.zIndex = "-1";
      document.body.style.overflow = "auto";
    },
  });
}

// ...existing code...
function createPost() {
  const submitBtn = document.querySelector(".create-post-button");

  if (submitBtn) {
    submitBtn.disabled = true;
  }

  const captionEl = document.getElementById("post-caption");
  const caption = captionEl ? captionEl.value.trim() : "";

  const anonymousCheckbox = document.querySelector(
    '#anonymousSwitch input[type="checkbox"]'
  );
  const isAnonymous = !!(anonymousCheckbox && anonymousCheckbox.checked);

  const photosInput = document.getElementById("post-media");
  const file =
    photosInput && photosInput.files && photosInput.files[0]
      ? photosInput.files[0]
      : null;
  const formData = new FormData();
  formData.append("caption", caption);
  formData.append("isAnonymous", isAnonymous ? "1" : "0");
  if (file) formData.append("media", file);

  //AJAX request to create post
  fetch(window.location.pathname, {
    method: "POST",
    body: formData,
    credentials: "same-origin",
  })
    .then(async (res) => {
      const text = await res.text();
      let json = null;
      try {
        json = text ? JSON.parse(text) : null;
      } catch (err) {
        // parsing failed — include raw response for debugging
        throw new Error(`Invalid JSON response: ${text}`);
      }
      if (!res.ok) {
        // server returned error status — prefer server message if present
        throw new Error(
          json && json.message ? json.message : `Server error (${res.status})`
        );
      }
      return json;
    })
    .then((json) => {
      if (json && json.success) {
        closeCreatePostModal();
        window.location.reload();
      } else {
        alert(json && json.message ? json.message : "Failed to create post");
      }
    })
    .catch((err) => {
      console.error("createPost error:", err);
      alert("Error creating post: " + err.message);
    })
    .finally(() => {
      if (submitBtn) submitBtn.disabled = false;
    });
}
