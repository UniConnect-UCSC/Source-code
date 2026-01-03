const router = new window.NotificationRouter();
const notificationContainer = document.getElementById('notificationsContainer');

//router.register('like', LikeNotificationRenderer);

// Infinity scroll initialization
const newNotificationScroll = new InfinityScroll(
  'getNotifications',
  '/Notifications/scrollable',
  notificationContainer,
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

  newNotificationScroll.loadNextElements().then(() => {
    updateNotificationTimeAgo(notificationContainer);
  });

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

function updateNotificationTimeAgo(notificationContainer) {
  const notificationItems = notificationContainer.querySelectorAll(".notification-item");
  
  notificationItems.forEach(item => {
    const timestamp = item.getAttribute('data-timestamp');
    const timeAgo = calculateTimeAgo(timestamp);
    const timeElement = item.querySelector('.notification-time');
    if (timeElement) {
      timeElement.textContent = timeAgo;
    }
  });
}


function markAllAsRead() {
  Ajax.jsonPost('/notifications/markAllAsRead', {}).then(() => {
    // instead of a refresh we can just update the classes of all notification items to reduce server load
    newNotificationScroll.refresh();
  });
}
