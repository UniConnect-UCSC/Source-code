function escapeHtml(text) {
  const div = document.createElement("div");
  div.textContent = text ?? "";
  return div.innerHTML;
}

function viewProof(urlValue) {
  if (!urlValue) return;

  // Support both encoded and plain URLs safely
  let url = urlValue;
  try {
    url = decodeURIComponent(urlValue);
  } catch (_) {
    url = urlValue;
  }

  window.open(url, "_blank", "noopener,noreferrer");
}

async function approveRequest(requestId, btn) {
  if (!confirm("Accept this representative request?")) return;

  btn.disabled = true;
  const originalText = btn.textContent;
  btn.textContent = "Accepting...";

  try {
    const res = await Ajax.jsonPost("/admin/approveRepRequest", {
      request_id: requestId,
    });

    if (res?.success) {
      btn.closest("tr")?.remove();
    } else {
      alert(res?.message || "Failed to accept request.");
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

async function removeRep(repId, btn) {
  if (!confirm("Remove this university representative?")) return;

  btn.disabled = true;
  const originalText = btn.textContent;
  btn.textContent = "Removing...";

  try {
    const res = await Ajax.jsonPost("/admin/removeUniRep", {
      rep_id: repId,
    });

    if (res?.success) {
      btn.closest("tr")?.remove();
    } else {
      alert(res?.message || "Failed to remove representative.");
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

function renderUniRepRow(item, type) {
  const tr = document.createElement("tr");

  if (type === "reps") {
    const registeredAt = new Date(item.registered_at).toLocaleDateString(
      "en-US",
      { year: "numeric", month: "short", day: "numeric" },
    );

    const person = `${item.rep_f_name ?? ""} ${item.rep_l_name ?? ""}`.trim();
    const encodedProofUrl = encodeURIComponent(item.proof_url || "");

    tr.innerHTML = `
      <td>${item.id ?? "-"}</td>
      <td>${escapeHtml(item.university_name || "-")}</td>
      <td>${escapeHtml(person || "-")}</td>
      <td>
        <button class="table-btn view-proof-btn" onclick="viewProof('${encodedProofUrl}')">
          View Proof
        </button>
      </td>
      <td>
        <span class="status-pill status-active">Representative</span>
      </td>
      <td>${registeredAt}</td>
      <td>
        <button class="table-btn remove-btn" onclick="removeRep('${item.id}', this)">
          Remove
        </button>
      </td>
    `;

    return tr;
  }

  const requestedAt = new Date(item.requested_at).toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });

  const person =
    `${item.student_f_name ?? ""} ${item.student_l_name ?? ""}`.trim();
  const encodedProofUrl = encodeURIComponent(item.proof_url || "");

  tr.innerHTML = `
    <td>${item.id ?? "-"}</td>
    <td>${escapeHtml(item.university_name || "-")}</td>
    <td>${escapeHtml(person || "-")}</td>
    <td>
      <button class="table-btn view-proof-btn" onclick="viewProof('${encodedProofUrl}')">
        View Proof
      </button>
    </td>
    <td>
      <span class="status-pill status-pending">${escapeHtml(item.status || "pending")}</span>
    </td>
    <td>${requestedAt}</td>
    <td>
      <button class="table-btn accept-btn" onclick="approveRequest(${item.id}, this)">
        Accept
      </button>
    </td>
  `;

  return tr;
}

function initUniRepTable() {
  const body = document.getElementById("uni-rep-table-body");
  const toggleButtons = document.querySelectorAll(".uni-rep-toggle-btn");
  if (!body) return;

  let currentType = "pending";
  const initialRows = body.children.length;

  const uniRepScroll = new InfinityScroll(
    "getAdminUniRep",
    "/admin/uniRepScrollable",
    body,
    (item) => renderUniRepRow(item, currentType),
    initialRows,
    10,
  );

  uniRepScroll.setupAutoLoadOnScroll();

  const lastRow = body.lastElementChild;
  if (lastRow && uniRepScroll.observer) {
    uniRepScroll.observer.observe(lastRow);
    uniRepScroll.prevObserverElement = lastRow;
  }

  toggleButtons.forEach((btn) => {
    btn.addEventListener("click", () => {
      const nextType = btn.getAttribute("data-type");
      if (!nextType || nextType === currentType) return;

      currentType = nextType;
      toggleButtons.forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");

      uniRepScroll.offset = 0;
      uniRepScroll.refresh({ type: currentType });
    });
  });
}

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initUniRepTable);
} else {
  initUniRepTable();
}
