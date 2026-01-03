const router = new window.NotificationRouter();
const notificationContainer = document.getElementById('notificationsContainer');
var isNotificationPanelOpen = false;
var checkingNewNotifications = false;

//router.register('like', LikeNotificationRenderer);

// Infinity scroll initialization
const newNotificationScroll = new InfinityScroll(
  'getNotifications',
  '/Notifications/scrollable',
  notificationContainer,
  router.render.bind(router),
  0,
  5
);

function refreshNotifications() {
  newNotificationScroll.refresh().then(() => {
    updateNotificationTimeAgo(notificationContainer);
  });
}

function loadMoreNotifications() {
  newNotificationScroll.loadNextElements().then(() => {
    updateNotificationTimeAgo(notificationContainer);
  });
}

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

  isNotificationPanelOpen = true;

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

  isNotificationPanelOpen = false;

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

function checkForNewNotifications(){

  if(checkingNewNotifications || isNotificationPanelOpen){return} 
  checkingNewNotifications = true;

  const mostRecentNotification = notificationContainer.querySelector('*'); 

  if(!mostRecentNotification) {
    checkingNewNotifications = false;
    return;
  }

  const data = {
    lastCheckTimestamp: mostRecentNotification.getAttribute('data-timestamp')
  };

  Ajax.jsonPost('/notifications/checkNew', data).then((data) => {

    if(data.hasNewNotifications){

      console.log("new Notifications available. Refreshing Notifications")
      refreshNotifications();
    }
  });

  checkingNewNotifications = false;
}

// Init first notifications
refreshNotifications();

const pollInterval = setInterval(() => {
  checkForNewNotifications();
}, 10000);