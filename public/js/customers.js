'use strict';

document.addEventListener('DOMContentLoaded', () => {

    let customers      = [];
    let searchQuery    = '';
    let activeFilters  = ['Bronze', 'Silver', 'Gold'];
    let sortCol        = 'points';
    let sortDir        = 'desc';
    let currentPage    = 1;
    let editingId      = null;
    let actionTargetId = null;
    const ROWS_PER_PAGE = 10;

    const tbody          = document.getElementById('customerTableBody');
    const emptyState     = document.getElementById('emptyState');
    const tableCount     = document.getElementById('tableCount');
    const pagination     = document.getElementById('pagination');
    const searchInput    = document.getElementById('searchInput');
    const btnFilter      = document.getElementById('btnFilter');
    const filterDropdown = document.getElementById('filterDropdown');
    const btnApplyFilter = document.getElementById('btnApplyFilter');
    const btnAdd         = document.getElementById('btnAddCustomer');
    const modalBackdrop  = document.getElementById('modalBackdrop');
    const modalTitle     = document.getElementById('modalTitle');
    const modalClose     = document.getElementById('modalClose');
    const btnCancel      = document.getElementById('btnCancel');
    const btnSave        = document.getElementById('btnSave');
    const actionMenu     = document.getElementById('actionMenu');
    const actionEdit     = document.getElementById('actionEdit');
    const actionPoints   = document.getElementById('actionPoints');
    const actionDelete   = document.getElementById('actionDelete');
    const pointsBackdrop = document.getElementById('pointsBackdrop');
    const pointsClose    = document.getElementById('pointsClose');
    const pointsCancel   = document.getElementById('pointsCancel');
    const btnAddPoints   = document.getElementById('btnAddPoints');
    const fieldSpent     = document.getElementById('fieldSpent');

    async function load() {
        try {
            const res = await fetch('/api/customers');
            customers = await res.json();
            render();
        } catch (e) { console.error('Failed to load customers', e); }
    }

    load();

    /* ── Render ── */
    function getFiltered() {
        return customers.filter(c => {
            const q = searchQuery.toLowerCase();
            const matchSearch = !q || c.name.toLowerCase().includes(q) || (c.phone || '').includes(q);
            const matchTier   = activeFilters.includes(c.tier);
            return matchSearch && matchTier;
        });
    }

    function getSorted(list) {
        return [...list].sort((a, b) => {
            let va = a[sortCol], vb = b[sortCol];
            if (['points','total_visits','total_spent'].includes(sortCol)) { va = parseFloat(va); vb = parseFloat(vb); }
            else { va = String(va).toLowerCase(); vb = String(vb).toLowerCase(); }
            return sortDir === 'asc' ? (va > vb ? 1 : -1) : (va < vb ? 1 : -1);
        });
    }

    function render() {
        const filtered = getFiltered();
        const sorted   = getSorted(filtered);
        const total    = sorted.length;
        const pages    = Math.max(1, Math.ceil(total / ROWS_PER_PAGE));
        currentPage    = Math.min(currentPage, pages);
        const start    = (currentPage - 1) * ROWS_PER_PAGE;
        const paged    = sorted.slice(start, start + ROWS_PER_PAGE);

        emptyState.classList.toggle('hidden', paged.length > 0);

        tbody.innerHTML = paged.map(c => {
            const tier      = c.tier.toLowerCase();
            const discount  = tier === 'gold' ? '10%' : tier === 'silver' ? '5%' : null;
            const maxPts    = tier === 'gold' ? 1000 : tier === 'silver' ? 500 : 100;
            const barWidth  = Math.min(100, Math.round((c.points / maxPts) * 100));

            return `
            <tr data-id="${c.id}">
                <td>
                    <div class="customer-name-wrap">
                        <span class="cname">${esc(c.name)}</span>
                        <span class="csub">${c.email ? esc(c.email) : 'No email'}</span>
                    </div>
                </td>
                <td>${c.phone ? esc(c.phone) : '<span style="color:#c8b8a8">—</span>'}</td>
                <td><span class="tier-badge ${tier}">${c.tier}</span></td>
                <td>
                    <div class="points-wrap">
                        <span class="points-num">${c.points.toLocaleString()} pts</span>
                        <div class="points-bar-bg"><div class="points-bar ${tier}" style="width:${barWidth}%"></div></div>
                    </div>
                </td>
                <td>${c.total_visits}</td>
                <td>₱${parseFloat(c.total_spent).toLocaleString('en-PH', { minimumFractionDigits: 2 })}</td>
                <td>${discount
                    ? `<span class="discount-badge">${discount} off</span>`
                    : `<span class="discount-badge none">None</span>`}
                </td>
                <td>
                    <button class="btn-action-menu" data-id="${c.id}">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><circle cx="12" cy="5" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="12" cy="19" r="1"/></svg>
                    </button>
                </td>
            </tr>`;
        }).join('');

        tableCount.textContent = `Showing ${total} customer${total !== 1 ? 's' : ''}`;
        renderPagination(pages);
        updateStats();
    }

    function renderPagination(pages) {
        if (pages <= 1) { pagination.innerHTML = ''; return; }
        pagination.innerHTML = Array.from({ length: pages }, (_, i) =>
            `<button class="page-btn ${i + 1 === currentPage ? 'active' : ''}" data-page="${i + 1}">${i + 1}</button>`
        ).join('');
        pagination.querySelectorAll('.page-btn').forEach(btn =>
            btn.addEventListener('click', () => { currentPage = +btn.dataset.page; render(); })
        );
    }

    function updateStats() {
        document.getElementById('statTotal').textContent  = customers.length;
        document.getElementById('statGold').textContent   = customers.filter(c => c.tier === 'Gold').length;
        document.getElementById('statSilver').textContent = customers.filter(c => c.tier === 'Silver').length;
        document.getElementById('statPoints').textContent = customers.reduce((s, c) => s + c.points, 0).toLocaleString();
    }

    /* ── Search ── */
    searchInput.addEventListener('input', () => { searchQuery = searchInput.value.trim(); currentPage = 1; render(); });

    /* ── Filter ── */
    btnFilter.addEventListener('click', e => { e.stopPropagation(); filterDropdown.classList.toggle('hidden'); });
    document.addEventListener('click', e => {
        if (!filterDropdown.contains(e.target) && e.target !== btnFilter) filterDropdown.classList.add('hidden');
    });
    btnApplyFilter.addEventListener('click', () => {
        activeFilters = [...filterDropdown.querySelectorAll('input:checked')].map(cb => cb.value);
        currentPage = 1; filterDropdown.classList.add('hidden'); render();
    });

    /* ── Sort ── */
    document.querySelectorAll('th.sortable').forEach(th => {
        th.addEventListener('click', () => {
            const col = th.dataset.col;
            sortDir = sortCol === col ? (sortDir === 'asc' ? 'desc' : 'asc') : 'desc';
            sortCol = col;
            document.querySelectorAll('.sort-arrow').forEach(a => a.className = 'sort-arrow');
            const arrow = th.querySelector('.sort-arrow');
            if (arrow) arrow.className = `sort-arrow ${sortDir}`;
            render();
        });
    });

    /* ── Action Menu ── */
    document.addEventListener('click', e => {
        const btn = e.target.closest('.btn-action-menu');
        if (btn) {
            e.stopPropagation();
            actionTargetId = +btn.dataset.id;
            const rect = btn.getBoundingClientRect();
            actionMenu.classList.remove('hidden');
            actionMenu.style.top  = (rect.bottom + 6 + window.scrollY) + 'px';
            actionMenu.style.left = (rect.left - 140 + window.scrollX) + 'px';
        } else if (!actionMenu.contains(e.target)) {
            actionMenu.classList.add('hidden');
        }
    });

    actionEdit.addEventListener('click', () => { actionMenu.classList.add('hidden'); openModal(actionTargetId); });
    actionPoints.addEventListener('click', () => { actionMenu.classList.add('hidden'); openPointsModal(actionTargetId); });
    actionDelete.addEventListener('click', async () => {
        actionMenu.classList.add('hidden');
        if (!confirm('Delete this customer?')) return;
        try {
            const res = await fetch('/api/customers/delete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf() },
                body: JSON.stringify({ id: actionTargetId })
            });
            const data = await res.json();
            if (data.success) load();
        } catch (e) { alert('Failed to delete customer'); }
    });

    /* ── Add/Edit Modal ── */
    function openModal(id = null) {
        editingId = id;
        const c = id ? customers.find(c => c.id === id) : null;
        modalTitle.textContent = c ? 'Edit Customer' : 'Add New Customer';
        document.getElementById('fieldName').value  = c ? c.name  : '';
        document.getElementById('fieldPhone').value = c ? (c.phone || '') : '';
        document.getElementById('fieldEmail').value = c ? (c.email || '') : '';
        modalBackdrop.classList.remove('hidden');
        document.getElementById('fieldName').focus();
    }

    function closeModal() { modalBackdrop.classList.add('hidden'); editingId = null; }

    btnAdd.addEventListener('click', () => openModal());
    modalClose.addEventListener('click', closeModal);
    btnCancel.addEventListener('click', closeModal);
    modalBackdrop.addEventListener('click', e => { if (e.target === modalBackdrop) closeModal(); });

    btnSave.addEventListener('click', async () => {
        const name  = document.getElementById('fieldName').value.trim();
        const phone = document.getElementById('fieldPhone').value.trim();
        const email = document.getElementById('fieldEmail').value.trim();
        if (!name) { alert('Name is required.'); return; }

        const payload = { name, phone: phone || null, email: email || null };
        if (editingId) payload.id = editingId;

        try {
            const res = await fetch('/api/customers/save', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf() },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (data.success) { closeModal(); load(); }
            else alert('Error: ' + (data.message || 'Unknown error'));
        } catch (e) { alert('Failed to save customer'); }
    });

    /* ── Points Modal ── */
    function openPointsModal(id) {
        const c = customers.find(c => c.id === id);
        if (!c) return;
        actionTargetId = id;
        document.getElementById('pointsCustomerName').textContent = c.name;
        document.getElementById('pointsInfo').innerHTML = `
            <div class="points-info-item"><span class="points-info-label">Current Points</span><span class="points-info-value">${c.points.toLocaleString()}</span></div>
            <div class="points-info-item"><span class="points-info-label">Tier</span><span class="points-info-value">${c.tier}</span></div>
            <div class="points-info-item"><span class="points-info-label">Visits</span><span class="points-info-value">${c.total_visits}</span></div>
            <div class="points-info-item"><span class="points-info-label">Total Spent</span><span class="points-info-value">₱${parseFloat(c.total_spent).toLocaleString('en-PH', {minimumFractionDigits:2})}</span></div>
        `;
        fieldSpent.value = '';
        document.getElementById('pointsPreview').classList.add('hidden');
        pointsBackdrop.classList.remove('hidden');
        fieldSpent.focus();
    }

    function closePointsModal() { pointsBackdrop.classList.add('hidden'); }

    pointsClose.addEventListener('click', closePointsModal);
    pointsCancel.addEventListener('click', closePointsModal);
    pointsBackdrop.addEventListener('click', e => { if (e.target === pointsBackdrop) closePointsModal(); });

    fieldSpent.addEventListener('input', () => {
        const spent   = parseFloat(fieldSpent.value) || 0;
        const earned  = Math.floor(spent / 10);
        const preview = document.getElementById('pointsPreview');
        if (earned > 0) {
            document.getElementById('previewEarned').textContent = earned;
            preview.classList.remove('hidden');
        } else {
            preview.classList.add('hidden');
        }
    });

    btnAddPoints.addEventListener('click', async () => {
        const spent = parseFloat(fieldSpent.value);
        if (!spent || spent <= 0) { alert('Enter a valid amount.'); return; }
        try {
            const res = await fetch('/api/customers/add-points', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf() },
                body: JSON.stringify({ id: actionTargetId, spent })
            });
            const data = await res.json();
            if (data.success) { closePointsModal(); load(); }
            else alert('Error: ' + data.message);
        } catch (e) { alert('Failed to add points'); }
    });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') { closeModal(); closePointsModal(); }
    });

    /* ── Helpers ── */
    function esc(str) {
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
    function csrf() { return document.querySelector('meta[name="csrf-token"]').content; }
});
