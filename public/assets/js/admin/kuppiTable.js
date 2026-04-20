function escapeHtml(text) {
  var div = document.createElement("div");
  div.textContent = text == null ? "" : text;
  return div.innerHTML;
}

function getKuppiStatusClass(status) {
  var normalized = String(status || "unknown")
    .trim()
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, "-");
  return normalized || "unknown";
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

async function resolveKuppiReport(kuppiId, action, btn) {
  var actionLabel =
    action === "approve" ? "allow this kuppi" : "reject this kuppi";
  if (!confirm("Are you sure you want to " + actionLabel + "?")) return;

  btn.disabled = true;
  var originalText = btn.textContent;
  btn.textContent = "Processing...";

  try {
    var res = await Ajax.jsonPost("/admin/resolveKuppiReport", {
      kuppi_id: kuppiId,
      action: action,
    });

    if (res && res.success) {
      var row = btn.closest("tr");
      if (row) {
        var reportStatusCell = row.querySelector(".report-status-cell");
        var decisionCell = row.querySelector(".decision-cell");
        var actionsCell = btn.closest("td");

        if (reportStatusCell) {
          reportStatusCell.innerHTML =
            '<span class="kuppi-status-pill status-resolved">Resolved</span>';
        }

        if (decisionCell) {
          decisionCell.innerHTML =
            '<span class="kuppi-status-pill status-' +
            getKuppiStatusClass(res.decision || "unknown") +
            '">' +
            escapeHtml(res.decision || "-") +
            "</span>";
        }

        if (actionsCell) {
          actionsCell.innerHTML = "<span>-</span>";
        }
      }
    } else {
      alert((res && res.message) || "Failed to resolve report.");
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
  var reportStatus = item.report_status || "Pending";
  var decision = item.decision || "";

  var actionHtml =
    reportStatus === "Pending"
      ? '<button class="table-btn allow-btn" onclick="resolveKuppiReport(\'' +
        escapeHtml(item.id) +
        "', 'approve', this)\">Allow</button> " +
        '<button class="table-btn reject-btn" onclick="resolveKuppiReport(\'' +
        escapeHtml(item.id) +
        "', 'reject', this)\">Reject</button>"
      : "<span>-</span>";

  var decisionHtml =
    decision !== ""
      ? '<span class="kuppi-status-pill status-' +
        escapeHtml(getKuppiStatusClass(decision)) +
        '">' +
        escapeHtml(decision) +
        "</span>"
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
    escapeHtml(item.host_university || "-") +
    "</td>" +
    "<td>" +
    escapeHtml(item.category_name || "-") +
    "</td>" +
    "<td>" +
    (item.report_count ?? 0) +
    "</td>" +
    "<td>" +
    escapeHtml(formatDateTime(item.last_reported_at)) +
    "</td>" +
    '<td class="report-status-cell">' +
    '<span class="kuppi-status-pill status-' +
    escapeHtml(getKuppiStatusClass(reportStatus)) +
    '">' +
    escapeHtml(reportStatus) +
    "</span>" +
    "</td>" +
    '<td class="decision-cell">' +
    decisionHtml +
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

  kuppiScroll.setContextProvider(function () {
    return {
      reportStatus: "",
      search: "",
    };
  });

  kuppiScroll.setSkeletonLoader(function () {
    var tr = document.createElement("tr");
    tr.innerHTML =
      '<td colspan="10" style="text-align:center; padding:1rem; color: var(--color-secondary-gray);">Loading...</td>';
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
