const router = new window.NotificationRouter();
const notificationContainer = document.getElementById('notificationsContainer');
var isNotificationPanelOpen = false;
var checkingNewNotifications = false;
var isFiltering = false;
var notificationFilter = 'all';
const loadMoreThreshold = 20;

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

async function refreshNotifications() {
  await newNotificationScroll.refresh(notificationFilter);

  updateNotificationTimeAgo(notificationContainer);
  updateNotificationCount();

  // If after refresh the container is not scrollable, try loading more notifications
  while (!checkIfContainerIsScrollable()) {
    if(!(await loadMoreNotifications())) {break;}
  }
}

async function loadMoreNotifications() {
  const endNotReached = await newNotificationScroll.loadNextElements(notificationFilter);
  updateNotificationTimeAgo(notificationContainer);

  return endNotReached;
}

function checkIfContainerIsScrollable() {
  if (notificationContainer.scrollHeight <= notificationContainer.clientHeight) {
    return false;
  }
  return true;
}

function updateNotificationCount() {
  
  Ajax.jsonPost('/notifications/getUnreadNotificationCount', {}).then((data) => {

  const count = data.unreadNotificationCount;
  const countBadge = document.getElementById('notificationCount');
  
  if (countBadge) {
    if (count > 0) {
      const displayCount = count > 99 ? '99+' : count.toString();
      countBadge.textContent = displayCount;
      countBadge.classList.remove('hidden');
    } else {
      countBadge.textContent = '';
      countBadge.classList.add('hidden');
    }
  }


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
    refreshNotifications();
  });
}

function filterNotifications(filter, parentElement) {
  
  if(isFiltering || notificationFilter === filter) {return;}
  isFiltering = true;

  notificationFilter = filter;

  const filterButtons = parentElement.querySelectorAll('.notification-filter-btn');
  filterButtons.forEach(btn => {
    if (btn.getAttribute('data-filter') === filter) {
      btn.classList.add('active');
    } else {
      btn.classList.remove('active');
    }
  });

  refreshNotifications().then(() => {
    isFiltering = false;
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

// Load more on scroll to bottom
notificationContainer.addEventListener('scroll', () => {  
  if (notificationContainer.scrollTop + notificationContainer.clientHeight >= notificationContainer.scrollHeight - loadMoreThreshold) {
    loadMoreNotifications();
  }
});