document.addEventListener('DOMContentLoaded', () => {
  loadList();

  document.getElementById('searchBox').addEventListener('input', debounce(()=>loadList(), 300));
  document.getElementById('perPage').addEventListener('change', ()=>loadList());
  document.getElementById('filterStatus').addEventListener('change', ()=>loadList());
});

async function loadList(page = 1) {
  const perPage = document.getElementById('perPage').value || 20;
  const search = encodeURIComponent(document.getElementById('searchBox').value || '');
  const status = document.getElementById('filterStatus').value || '';

  const res = await fetch(`${INVOICE_AJAX_URL}?per_page=${perPage}&page=${page}&search=${search}&status=${status}`);
  const json = await res.json();
  const tbody = document.getElementById('dataBody');
  tbody.innerHTML = '';

  json.data.forEach(inv => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${inv.id}</td>
      <td><a href="/billing/invoices/${inv.id}">${inv.invoice_no}</a></td>
      <td>${inv.invoice_date ?? ''}</td>
      <td>${formatAmount(inv.total)}</td>
      <td>${formatAmount(inv.paid_amount)}</td>
      <td>${formatAmount(inv.balance)}</td>
      <td>${inv.status}</td>
      <td>
         <a class="btn btn-sm btn-outline-primary" href="/billing/invoices/${inv.id}">View</a>
      </td>
    `;
    tbody.appendChild(tr);
  });

  renderPagination(json);
}

function renderPagination(json) {
  const ul = document.getElementById('pagination');
  ul.innerHTML = '';
  for (let p = 1; p <= json.last_page; p++) {
    const li = document.createElement('li');
    li.className = `page-item ${p === json.current_page ? 'active' : ''}`;
    li.innerHTML = `<a class="page-link" href="javascript:void(0)" onclick="loadList(${p})">${p}</a>`;
    ul.appendChild(li);
  }
}

function formatAmount(v){ return v === null ? '-' : parseFloat(v).toFixed(2); }

function debounce(fn, wait=300) {
  let t;
  return function(...a){ clearTimeout(t); t = setTimeout(()=>fn.apply(this,a), wait); };
}
