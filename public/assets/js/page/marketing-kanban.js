// call when user clicks "Show Kanban"
document.getElementById("toggleKanban").addEventListener("click", async function() {
    const container = document.getElementById('kanbanContainer');
    if (container.style.display === 'none') {
        container.style.display = 'flex';
        await loadKanban();
        this.textContent = 'Hide Kanban';
    } else {
        container.style.display = 'none';
        this.textContent = 'Show Kanban';
    }
});

async function loadKanban() {
    const res = await fetch('/marketing/leads/kanban');
    const data = await res.json();

    const container = document.getElementById('kanbanContainer');
    container.innerHTML = ''; // clear

    for (const statusKey in data) {
        const column = document.createElement('div');
        column.className = 'card p-2 m-2';
        column.style.flex = '1';
        column.dataset.status = statusKey;

        const header = document.createElement('div');
        header.innerHTML = `<strong>${window.LEAD_STATUS[statusKey] || statusKey}</strong>`;
        column.appendChild(header);

        const list = document.createElement('div');
        list.className = 'kanban-list mt-2';
        list.dataset.status = statusKey;
        list.ondragover = ev => ev.preventDefault();
        list.ondrop = ev => onDrop(ev, statusKey);

        (data[statusKey] || []).forEach(item => {
            const card = document.createElement('div');
            card.className = 'card mb-2 p-2';
            card.draggable = true;
            card.ondragstart = ev => ev.dataTransfer.setData('text/plain', item.id);
            card.innerHTML = `<div><strong>${item.lead_code}</strong></div><div>${item.name} - ${item.mobile}</div>`;
            list.appendChild(card);
        });

        column.appendChild(list);
        container.appendChild(column);
    }
}

async function onDrop(ev, toStatus) {
    ev.preventDefault();
    const leadId = ev.dataTransfer.getData('text/plain');
    if (!leadId) return;
    await fetch(`/marketing/leads/${leadId}/move`, {
        method: 'POST',
        headers: {
            'Content-Type':'application/json',
            'X-CSRF-TOKEN': TOKEN
        },
        body: JSON.stringify({ status: toStatus })
    });
    await loadKanban();
}
