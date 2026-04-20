function escapeHtml(text) {
  const div = document.createElement("div");
  div.textContent = text ?? "";
  return div.innerHTML;
}

function formatDateTime(value) {
  if (!value) return "-";
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) return "-";
  return date.toLocaleString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
}

async function removeEvent(eventId, btn) {
  console.log("Removing event ID:", eventId, typeof eventId);
  if (!confirm("Are you sure you want to remove this event?")) return;

  btn.disabled = true;

  try {
    const res = await fetch("/admin/removeEvent", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
      credentials: "same-origin",
      body: JSON.stringify({ event_id: eventId }),
    });

    const text = await res.text();
    console.log("Raw response:", text);
  } catch (e) {
    console.error(e);
  }
}

function renderEventRow(event) {
  const tr = document.createElement("tr");

  tr.innerHTML = `
    <td>${event.id ?? "-"}</td>
    <td>${escapeHtml(event.title || "-")}</td>
    <td>${escapeHtml(event.university_name || "-")}</td>
    <td>${escapeHtml(event.held_at || "-")}</td>
    <td>${escapeHtml(formatDateTime(event.event_timestamp))}</td>
    <td>${event.participant_count ?? 0}</td>
    <td>
      <button class="table-btn remove-btn" onclick="removeEvent('${event.id}', this)">
        Remove
      </button>
    </td>
  `;

  return tr;
}

function initEventsTable() {
  const body = document.getElementById("events-table-body");
  if (!body) return;

  const initialRows = body.children.length;

  const eventsScroll = new InfinityScroll(
    "getAdminEvents",
    "/admin/eventsScrollable",
    body,
    renderEventRow,
    initialRows,
    10,
  );

  eventsScroll.setSkeletonLoader(() => {
    const tr = document.createElement("tr");
    tr.innerHTML = `
      <td colspan="7" style="text-align:center; padding:1rem; color: var(--color-secondary-gray);">
        Loading...
      </td>
    `;
    return tr;
  });

  eventsScroll.setupAutoLoadOnScroll();

  const lastRow = body.lastElementChild;
  if (lastRow && eventsScroll.observer) {
    eventsScroll.observer.observe(lastRow);
    eventsScroll.prevObserverElement = lastRow;
  }
}

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initEventsTable);
} else {
  initEventsTable();
}
