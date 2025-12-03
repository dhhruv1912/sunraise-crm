async function customerSearch(inputId, hiddenId, onSelect = null) {
    const input = document.getElementById(inputId);
    const hidden = document.getElementById(hiddenId);

    let dropdown;

    input.addEventListener("input", async () => {
        const q = input.value.trim();
        if (q.length < 2) return;

        const res = await fetch(`/customers/search?q=${q}`);
        const items = await res.json();

        if (dropdown) dropdown.remove();

        dropdown = document.createElement("div");
        dropdown.className = "dropdown-menu show w-100";
        dropdown.style.position = "absolute";
        dropdown.style.zIndex = "1000";

        items.forEach(row => {
            const a = document.createElement("button");
            a.className = "dropdown-item";
            a.textContent = `${row.name} — ${row.mobile}`;
            a.onclick = () => {
                input.value = row.text;
                hidden.value = row.id;

                if (onSelect) onSelect(row);

                dropdown.remove();
            };
            dropdown.appendChild(a);
        });

        input.parentElement.appendChild(dropdown);
    });

    document.addEventListener("click", (e) => {
        if (dropdown && !input.contains(e.target)) dropdown.remove();
    });
}
