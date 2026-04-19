function escapeHtml(text) {
  var div = document.createElement("div");
  div.textContent = text == null ? "" : text;
  return div.innerHTML;
}

function formatDateTime(value) {
  if (!value) return "-";
  var date = new Date(value);
  if (Number.isNaN(date.getTime())) return "-";
  return date.toLocaleString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
}

function renderKuppiRow(item) {
  var tr = document.createElement("tr");
  var host = ((item.host_f_name || "") + " " + (item.host_l_name || "")).trim();

  tr.innerHTML =
    "<td>" +
    (item.id ?? "-") +
    "</td>" +
    "<td>" +
    escapeHtml(item.topic || "-") +
    "</td>" +
    "<td>" +
    escapeHtml(host || "-") +
    "</td>" +
    "<td>" +
    escapeHtml(item.university_name || "-") +
    "</td>" +
    "<td>" +
    escapeHtml(item.category_name || "-") +
    "</td>" +
    "<td>" +
    escapeHtml(item.status || "-") +
    "</td>" +
    "<td>" +
    escapeHtml(formatDateTime(item.kuppi_date_time)) +
    "</td>" +
    "<td>" +
    (item.participants ?? 0) +
    "</td>";

  return tr;
}

function initKuppiTable() {
  var body = document.getElementById("kuppi-table-body");
  if (!body) return;

  var initialRows = body.children.length;

  var kuppiScroll = new InfinityScroll(
    "getAdminKuppi",
    "/admin/kuppiScrollable",
    body,
    renderKuppiRow,
    initialRows,
    10,
  );

  kuppiScroll.setSkeletonLoader(function () {
    var tr = document.createElement("tr");
    tr.innerHTML =
      '<td colspan="8" style="text-align:center; padding:1rem; color: var(--color-secondary-gray);">Loading...</td>';
    return tr;
  });

  kuppiScroll.setupAutoLoadOnScroll();

  var lastRow = body.lastElementChild;
  if (lastRow && kuppiScroll.observer) {
    kuppiScroll.observer.observe(lastRow);
    kuppiScroll.prevObserverElement = lastRow;
  }
}

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initKuppiTable);
} else {
  initKuppiTable();
}
