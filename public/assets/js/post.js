async function reactOnPost(postId) {
  const btn = document.querySelector(`.like-btn[data-post-id="${postId}"]`);

  try {
    const res = await Ajax.jsonPost("/posts/reactOnPost", { post_id: postId });

    console.log(res); // What does this show?

    if (res?.success === false) throw new Error(res.message || "Failed");

    btn.classList.toggle("liked", res.action === "added");
    btn.innerHTML = `<i data-lucide="thumbs-up"></i> ${res.reaction_count}`;
    lucide.createIcons();
  } catch (e) {
    console.error(e);
    alert("An error occurred. Please try again.");
  }
}
