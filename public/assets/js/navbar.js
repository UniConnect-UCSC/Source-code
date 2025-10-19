document.addEventListener("DOMContentLoaded", () => {
  console.log("navbar.js loaded");
  const profileContainer = document.querySelector(".profile__container");
  const profileContent = document.getElementById("user-content");
  let isOpen = false;

  gsap.set(profileContent, { autoAlpha: 0, y: -20, display: "none" });

  profileContainer.addEventListener("click", (e) => {
    // If clicking a link inside profile__content, let it proceed
    if (e.target.closest(".profile__content a")) return;

    e.stopPropagation();
    if (!isOpen) {
      gsap.set(profileContent, { display: "block" });
      gsap.to(profileContent, {
        autoAlpha: 1,
        y: 0,
        duration: 0.3,
        ease: "power2.out",
      });
      isOpen = true;
    } else {
      gsap.to(profileContent, {
        autoAlpha: 0,
        y: -20,
        duration: 0.2,
        ease: "power2.in",
        onComplete: () => gsap.set(profileContent, { display: "none" }),
      });
      isOpen = false;
    }
  });

  document.addEventListener("click", (e) => {
    if (isOpen && !profileContainer.contains(e.target)) {
      gsap.to(profileContent, {
        autoAlpha: 0,
        y: -20,
        duration: 0.2,
        ease: "power2.in",
        onComplete: () => gsap.set(profileContent, { display: "none" }),
      });
      isOpen = false;
    }
  });
});

ocument.addEventListener("DOMContentLoaded", () => {
  const input =
    document.querySelector(".search-wrapper .search") ||
    document.querySelector(".search");
  if (!input) return;
  input.addEventListener("keydown", (e) => {
    if (e.key === "Enter") {
      const q = input.value.trim();
      if (!q) return; // do nothing on empty query
      // redirect to search page with query param
      window.location.href = `/search?q=${encodeURIComponent(q)}`;
    }
  });
});
