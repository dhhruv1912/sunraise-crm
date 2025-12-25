// Simple toast popup
function toast(msg, type = "success") {
    const bg = type === "success" ? "var(--arham-color-blue)" : "#dc3545";

    const el = document.createElement("div");
    el.style.position = "fixed";
    el.style.bottom = "20px";
    el.style.right = "20px";
    el.style.background = bg;
    el.style.color = "white";
    el.style.padding = "10px 18px";
    el.style.borderRadius = "6px";
    el.style.zIndex = 99999;
    el.innerText = msg;

    document.body.appendChild(el);

    setTimeout(() => el.remove(), 2000);
}

// Open modal from AJAX content
function openModal(content) {
    const modalHtml = `
        <div class="modal fade" id="ajaxModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">${content}</div>
            </div>
        </div>
    `;

    document.querySelector("#ajaxModal")?.remove();
    document.body.insertAdjacentHTML("beforeend", modalHtml);

    const modal = new bootstrap.Modal(document.getElementById("ajaxModal"));
    modal.show();
}
