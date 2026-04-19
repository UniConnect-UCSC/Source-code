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

async function approveKuppiRequest(kuppiId, btn) {
  if (!confirm("Approve this requested kuppi?")) return;

  btn.disabled = true;
  const originalText = btn.textContent;
  btn.textContent = "Approving...";

  try {
    const res = await Ajax.jsonPost("/admin/approveKuppiRequest", {
      kuppi_id: kuppiId,
    });

    if (res?.success) {
      const row = btn.closest("tr");
      const statusCell = row?.querySelector(".kuppi-status-cell");
      if (statusCell) statusCell.textContent = "Upcoming";
      btn.closest("td").innerHTML = "<span>-</span>";
    } else {
      alert(res?.message || "Failed to approve kuppi request.");
      btn.disabled = false;
      btn.textContent = originalText;
    }
  } catch (e) {
    console.error(e);
    alert("An error occurred. Please try again.");
    btn.disabled = false;
    btn.textContent = originalText;
  }
}

function renderKuppiRow(item) {
  var tr = document.createElement("tr");
  var host = ((item.host_f_name || "") + " " + (item.host_l_name || "")).trim();

  var actionHtml =
    item.status === "Requested"
      ? '<button class="table-btn approve-btn" onclick="approveKuppiRequest(\'' +
        escapeHtml(item.id) +
        "', this)\">Approve</button>"
      : "<span>-</span>";

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
    '<td class="kuppi-status-cell">' +
    escapeHtml(item.status || "-") +
    "</td>" +
    "<td>" +
    escapeHtml(formatDateTime(item.kuppi_date_time)) +
    "</td>" +
    "<td>" +
    (item.participants ?? 0) +
    "</td>" +
    "<td>" +
    actionHtml +
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
      '<td colspan="9" style="text-align:center; padding:1rem; color: var(--color-secondary-gray);">Loading...</td>';
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
