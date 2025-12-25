const router = new window.NotificationRouter();

//router.register('like', LikeNotificationRenderer);

// Infinity scroll initialization
const newNotificationScroll = new InfinityScroll(
  'getNotifications',
  '/Notifications/scrollable',
  document.getElementById('notificationsContainer'),
  router.render.bind(router),
  0,
  6
);

function toggleNotifications() {
  const notificationsWrapper = document.querySelector(
    ".notifications-wrapper"
  );
  const isVisible = notificationsWrapper.style.opacity === "1";

  if (isVisible) {
    closeNotifications();
  } else {
    openNotifications();
  }
}

function openNotifications() {
  const notificationsWrapper = document.querySelector(
    ".notifications-wrapper"
  );

  newNotificationScroll.loadNextElements();

  gsap.to(notificationsWrapper, {
    duration: 0.2,
    opacity: 1,
    ease: "power2.out",
    zIndex: 2000,
    onStart: () => {
      // allow clicks once we're opening
      notificationsWrapper.style.pointerEvents = "auto";
    },
  });
}

function closeNotifications() {
  const notificationsWrapper = document.querySelector(
    ".notifications-wrapper"
  );

  gsap.to(notificationsWrapper, {
    duration: 0.2,
    opacity: 0,
    ease: "power2.in",
    zIndex: -20,
    onComplete: () => {
      // prevent the hidden container from intercepting clicks
      notificationsWrapper.style.pointerEvents = "none";
    },
  });
}


function markAllAsRead() {
  Ajax.jsonPost('/notifications/markAllAsRead', {}).then(() => {
    // instead of a refresh we can just update the classes of all notification items to reduce server load
    newNotificationScroll.refresh();
  });
}
