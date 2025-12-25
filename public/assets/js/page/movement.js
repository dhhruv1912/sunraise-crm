function movePanels(panelIds, warehouseId, note = "") {
    fetch("/panels/move", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            panel_ids: panelIds,
            to_warehouse_id: warehouseId,
            note
        })
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            toast("Panels moved successfully");
            loadPanels(panelsPage);
        } else {
            toast(res.message, "error");
        }
    });
}
