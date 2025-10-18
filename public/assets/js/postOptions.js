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

function deletePost(btn) {
  event.stopPropagation();
  const menu = btn.closest(".post-more-options-menu");
  const postId = menu.getAttribute("data-post-id");
  if (confirm("Are you sure you want to delete this post? ")) {
    fetch(window.location.pathname, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "delete_post_id=" + encodeURIComponent(postId),
    }).then(() => window.location.reload());
  }
}
