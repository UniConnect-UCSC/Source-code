function toggleNotifications() {
  const notificationsContainer = document.querySelector(
    ".notifications-container"
  );
  const isVisible = notificationsContainer.style.opacity === "1";

  if (isVisible) {
    closeNotifications();
  } else {
    openNotifications();
  }
}

function openNotifications() {
  const notificationsContainer = document.querySelector(
    ".notifications-container"
  );

  console.log("Opening notifications");
  gsap.to(notificationsContainer, {
    duration: 0.2,
    opacity: 1,
    ease: "power2.out",
    zIndex: 2000,
  });
}

function closeNotifications() {
  const notificationsContainer = document.querySelector(
    ".notifications-container"
  );

  gsap.to(notificationsContainer, {
    duration: 0.2,
    opacity: 0,
    ease: "power2.in",
    zIndex: -1,
  });
}
