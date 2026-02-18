document.addEventListener("DOMContentLoaded", () => {


    document.getElementById("quote_master_id").addEventListener("change", updateQuoteMasterData);
    document.getElementById("updateQuoteMasterButton").addEventListener("click", changeQuoteMaster);
    document.getElementById("sendMailBtn").addEventListener("click", sendMail);
    document.getElementById("convertToLeadBtn").addEventListener("click", convertToLead);

    function updateQuoteMasterData() {
        id = document.getElementById('quote_master_id').value
        current_quote_master = MASTERS[id]
        
        document.getElementById("modal-sku").textContent = current_quote_master.sku
        document.getElementById("modal-module").textContent = current_quote_master.module
        document.getElementById("modal-kw").textContent = current_quote_master.kw
        document.getElementById("modal-module_count").textContent = current_quote_master.module_count
        document.getElementById("modal-value").textContent = current_quote_master.value
        document.getElementById("modal-taxes").textContent = current_quote_master.taxes
        document.getElementById("modal-metering_cost").textContent = current_quote_master.metering_cost
        document.getElementById("modal-mcb_ppa").textContent = current_quote_master.mcb_ppa
        document.getElementById("modal-payable").textContent = current_quote_master.payable
        document.getElementById("modal-subsidy").textContent = current_quote_master.subsidy
        document.getElementById("modal-projected").textContent = current_quote_master.projected

        if (QUOTE_MASTER_ID != id) {
            document.getElementById("updateQuoteMasterButton").classList.remove('d-none')
            document.getElementById("updateQuoteMasterButton").dataset.id = id
        } else {
            document.getElementById("updateQuoteMasterButton").classList.add('d-none')
        }
    }
    async function changeQuoteMaster() {
        quote_master_id = document.getElementById('quote_master_id').value
        if (!quote_master_id) return;
        const res = await fetch(`/quote/requests/${QUOTE_REQUEST_ID}/quote-master`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': TOKEN
            },
            body: JSON.stringify({
                quote_master_id
            })
        });
        const json = await res.json();
        if (json.status) {
            // optionally notify
            document.getElementById("updateQuoteMasterButton").classList.add('d-none')
            QUOTE_MASTER_ID = quote_master_id
            alert('Updated!');
            // loadData();
        } else {
            alert('Update failed');
        }
    }

    async function sendMail() {
        if (!confirm('Send quotation email to customer?')) return;
        try {
            const res = await fetch(`/quote/requests/${QUOTE_REQUEST_ID}/send-mail`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': TOKEN
                }
            });
            const json = await res.json();
            alert(json.message || 'Sent');
        } catch (err) {
            alert('Failed to send');
        }
    }


    async function convertToLead() {
        if (!confirm('Convert this request to lead?')) return;
        try {
            const res = await fetch(`/quote/requests/${QUOTE_REQUEST_ID}/convert-to-lead`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': TOKEN
                }
            });
            const json = await res.json();
            if (json.status) {
                alert('Converted to lead');
            } else alert('Failed');
        } catch (err) {
            alert('Error');
        }
    }
});
