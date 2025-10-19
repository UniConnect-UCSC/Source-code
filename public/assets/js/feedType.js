document.addEventListener("DOMContentLoaded", function () {
  const feedContainer = document.querySelector(".profile-feed");
  if (!feedContainer) return;

  const feedButtons = document.querySelectorAll(".feed-type-item");

  function showFeed(type) {
    document
      .querySelectorAll(".profile-feed-list")
      .forEach((el) => (el.style.display = "none"));
    if (type === "university") {
      const uni = document.querySelector(".profile-feed-university");
      if (uni) {
        uni.style.display = "";
        document
          .querySelector('.feed-type-item[data-feed="university"]')
          .classList.add("feed-type-item-active");
        document
          .querySelector('.feed-type-item[data-feed="global"]')
          .classList.remove("feed-type-item-active");
      }
    } else {
      const global = document.querySelector(".profile-feed-global");
      if (global) {
        global.style.display = "";
        document
          .querySelector('.feed-type-item[data-feed="global"]')
          .classList.add("feed-type-item-active");

        document
          .querySelector('.feed-type-item[data-feed="university"]')
          .classList.remove("feed-type-item-active");
      }
    }

    feedButtons.forEach((b) =>
      b.classList.toggle("active", b.dataset.feed === type)
    );
  }

  showFeed("global");

  feedButtons.forEach((btn) => {
    btn.addEventListener("click", (e) => {
      const type = btn.dataset.feed;
      if (!type) return;
      showFeed(type);
    });
  });
});
