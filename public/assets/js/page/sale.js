function sellPanels(panelIds, customerId, soldAt, note = "") {
    fetch("/panels/sell", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            panel_ids: panelIds,
            customer_id: customerId,
            sold_at: soldAt,
            note
        })
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            toast("Panels sold");
            loadPanels(panelsPage);
        } else toast(res.message, "error");
    });
}

function returnPanels(panelIds, warehouseId, note = "") {
    fetch("/panels/return", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            panel_ids: panelIds,
            warehouse_id: warehouseId,
            note
        })
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            toast("Panels returned");
            loadPanels(panelsPage);
        } else toast(res.message, "error");
    });
}
