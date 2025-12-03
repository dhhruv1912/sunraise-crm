{{-- resources/views/page/quote_requests/view_wrapper.blade.php --}}
@extends('temp.common') {{-- or @extends('temp.common') / your layout --}}

@section('title', 'Quote Request View')

@section('content')
    {{-- Minimal page wrapper — modal will show immediately --}}
    <div class="container py-4">
        {{-- Nothing else here, modal will be auto-opened --}}
    </div>

    <!-- Modal -->
    <div class="modal fade" id="qrViewModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
      <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              <span id="modal-type">—</span> Request — <span id="modal-id">#—</span>
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            <div class="row g-2">
              <div class="col-md-6">
                <dl class="row">
                  <dt class="col-sm-4">Name</dt><dd class="col-sm-8" id="modal-name">—</dd>
                  <dt class="col-sm-4">Mobile</dt><dd class="col-sm-8" id="modal-number">—</dd>
                  <dt class="col-sm-4">Email</dt><dd class="col-sm-8" id="modal-email">—</dd>
                  <div id="quote-fields">
                    <dt class="col-sm-4">Module</dt><dd class="col-sm-8" id="modal-module">—</dd>
                    <dt class="col-sm-4">KW</dt><dd class="col-sm-8" id="modal-kw">—</dd>
                    <dt class="col-sm-4">MC</dt><dd class="col-sm-8" id="modal-mc">—</dd>
                  </div>
                  <dt class="col-sm-4">Status</dt><dd class="col-sm-8" id="modal-status">—</dd>
                  <dt class="col-sm-4">Assigned</dt><dd class="col-sm-8" id="modal-assigned">—</dd>
                  <dt class="col-sm-4">Notes</dt><dd class="col-sm-8" id="modal-notes">—</dd>
                </dl>
              </div>

              <div class="col-md-6">
                <h6>Actions</h6>
                <div class="mb-2">
                  <button id="modal-send-mail" class="btn btn-outline-primary btn-sm">Send Mail</button>
                  <button id="modal-convert-lead" class="btn btn-success btn-sm">Convert to Lead</button>
                </div>

                <h6 class="mt-3">History</h6>
                <div id="modal-history" class="list-group small">
                  <!-- filled by JS -->
                </div>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <a href="{{ route('quote_requests.index') }}" class="btn btn-outline-secondary">Back</a>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
@endsection

@section('scripts')
<script>
(function(){
    const id = {{ (int) $id }};
    const modalEl = new bootstrap.Modal(document.getElementById('qrViewModal'), {});
    const apiUrl = `/quote/requests/${id}/view-json`;

    // safe helper for fetch JSON with CSRF header
    async function fetchJson(url, opts = {}) {
        opts.headers = Object.assign({'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json'}, opts.headers || {});
        // add CSRF token for POST requests
        if (!opts.method || opts.method.toUpperCase() === 'GET') {
            // nothing
        } else {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (token) opts.headers['X-CSRF-TOKEN'] = token;
        }
        const res = await fetch(url, opts);
        if (!res.ok) throw new Error('Network response was not ok: ' + res.status);
        return res.json();
    }

    function renderHistory(items = []) {
        const wrap = document.getElementById('modal-history');
        wrap.innerHTML = '';
        if (!items.length) {
            wrap.innerHTML = '<div class="text-muted small">No history</div>';
            return;
        }
        items.forEach(h => {
            const item = document.createElement('div');
            item.className = 'list-group-item list-group-item-action mb-1';
            item.innerHTML = `<div class="d-flex w-100 justify-content-between">
                                <small class="text-muted">${h.user || 'System'}</small>
                                <small class="text-muted">${h.datetime || ''}</small>
                              </div>
                              <div class="mt-1">${h.message || ''}</div>`;
            wrap.appendChild(item);
        });
    }

    function populateModal(resp) {
        if (!resp || !resp.data) return;
        const d = resp.data;

        document.getElementById('modal-id').textContent = d.id ?? '—';
        document.getElementById('modal-type').textContent = (d.type || '—').toUpperCase();
        document.getElementById('modal-name').textContent = d.name || '—';
        document.getElementById('modal-number').textContent = d.number || '—';
        document.getElementById('modal-email').textContent = d.email || '—';
        document.getElementById('modal-module').textContent = d.module || '—';
        document.getElementById('modal-kw').textContent = d.kw ?? '—';
        document.getElementById('modal-mc').textContent = d.mc ?? '—';
        document.getElementById('modal-status').textContent = (window.__QR_STATUS && window.__QR_STATUS[d.status]) ? window.__QR_STATUS[d.status] : (d.status || '—');
        document.getElementById('modal-assigned').textContent = resp.users && resp.users.find(u => u.id === d.assigned_to) ? (resp.users.find(u => u.id === d.assigned_to).fname + ' ' + (resp.users.find(u => u.id === d.assigned_to).lname || '')) : (d.assigned_to_name ?? '—');
        document.getElementById('modal-notes').textContent = d.notes || '—';

        // Show/hide quote-only fields
        document.getElementById('quote-fields').style.display = (d.type === 'quote') ? 'block' : 'none';

        // wire action buttons
        const sendBtn = document.getElementById('modal-send-mail');
        sendBtn.onclick = async function() {
            sendBtn.disabled = true;
            try {
                await fetchJson(`/quote/requests/${d.id}/send-mail`, { method: 'POST' });
                alert('Mail queued / sent');
            } catch (e) {
                console.error(e);
                alert('Mail failed');
            } finally { sendBtn.disabled = false; }
        };

        const convBtn = document.getElementById('modal-convert-lead');
        convBtn.onclick = async function() {
            convBtn.disabled = true;
            try {
                const res = await fetchJson(`/quote/requests/${d.id}/convert-to-lead`, { method: 'POST' });
                if (res.success || res.status) {
                    alert('Converted to lead');
                    // optionally redirect to lead edit
                } else {
                    alert('Failed to convert');
                }
            } catch (e) {
                console.error(e);
                alert('Failed to convert');
            } finally { convBtn.disabled = false; }
        };

        // render history
        renderHistory(resp.history || []);

        // show modal
        modalEl.show();
    }

    // initial load
    (async function(){
        try {
            const json = await fetchJson(apiUrl);
            // many controllers return { data, history, users } — handle both shapes
            if (json.data) {
                populateModal(json);
            } else {
                // older shape: direct model returned
                populateModal({ data: json, history: [] });
            }
        } catch (err) {
            console.error('Failed to load request', err);
            const body = document.querySelector('.modal-body');
            body.innerHTML = '<div class="alert alert-danger">Failed to load request data.</div>';
            modalEl.show();
        }
    })();

    // cleanup when modal closes (so user can navigate away)
    document.getElementById('qrViewModal').addEventListener('hidden.bs.modal', function () {
        // optional redirect back to listing if needed
        // window.location = '{{ route('quote_requests.index') }}';
    });
})();
</script>
@endsection
