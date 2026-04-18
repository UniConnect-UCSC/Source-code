async function getDashboardStats() {
  try {
    const res = await Ajax.request("GET", "/admin/dashboardStats");

    if (!res || !res.success) {
      console.error("Failed to fetch stats:", res);
      return;
    }

    const totalUsers = res.data.totalUsers;
    const totalGlobalPosts = res.data.totalGlobalPosts;
    const totalUniversityPosts = res.data.totalUniversityPosts;
    const totalComments = res.data.totalComments;

    const totalUsersEl = document.getElementById("total-users-value");
    if (totalUsersEl)
      totalUsersEl.textContent = (totalUsers ?? 0).toLocaleString();

    const totalGlobalPostsEl = document.getElementById(
      "total-global-posts-value",
    );
    if (totalGlobalPostsEl)
      totalGlobalPostsEl.textContent = (totalGlobalPosts ?? 0).toLocaleString();

    const totalUniversityPostsEl = document.getElementById(
      "total-university-posts-value",
    );
    if (totalUniversityPostsEl)
      totalUniversityPostsEl.textContent = (
        totalUniversityPosts ?? 0
      ).toLocaleString();

    const totalCommentsEl = document.getElementById("total-comments-value");
    if (totalCommentsEl)
      totalCommentsEl.textContent = (totalComments ?? 0).toLocaleString();
  } catch (error) {
    console.error("Failed to fetch dashboard stats:", error);
  }
}

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", getDashboardStats);
} else {
  getDashboardStats();
}
