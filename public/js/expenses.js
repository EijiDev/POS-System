'use strict';

document.addEventListener('DOMContentLoaded', () => {

    let expenses       = [];
    let searchQuery    = '';
    let activeFilters  = { categories: ['Stock','Utilities','Rent','Staff','Maintenance','Other'], statuses: ['Paid','Pending'] };
    let sortCol        = 'date';
    let sortDir        = 'desc';
    let currentPage    = 1;
    let editingId      = null;
    let actionTargetId = null;
    const ROWS_PER_PAGE = 10;

    const tbody          = document.getElementById('expenseTableBody');
    const emptyState     = document.getElementById('emptyState');
    const tableCount     = document.getElementById('tableCount');
    const pagination     = document.getElementById('pagination');
    const searchInput    = document.getElementById('searchInput');
    const btnFilter      = document.getElementById('btnFilter');
    const filterDropdown = document.getElementById('filterDropdown');
    const btnApplyFilter = document.getElementById('btnApplyFilter');
    const btnAdd         = document.getElementById('btnAddExpense');
    const modalBackdrop  = document.getElementById('modalBackdrop');
    const modalTitle     = document.getElementById('modalTitle');
    const modalClose     = document.getElementById('modalClose');
    const btnCancel      = document.getElementById('btnCancel');
    const btnSave        = document.getElementById('btnSave');
    const actionMenu     = document.getElementById('actionMenu');
    const actionEdit     = document.getElementById('actionEdit');
    const actionDelete   = document.getElementById('actionDelete');

    async function load() {
        try {
            const res = await fetch('/api/expenses');
            expenses = await res.json();
            render();
        } catch (e) {
            console.error('Failed to load expenses', e);
        }
    }

    load();

    /* ── Render ── */
    function getFiltered() {
        return expenses.filter(e => {
            const q = searchQuery.toLowerCase();
            const matchSearch = !q || e.description.toLowerCase().includes(q) || e.category.toLowerCase().includes(q);
            const matchCat    = activeFilters.categories.includes(e.category);
            const matchStatus = activeFilters.statuses.includes(e.status);
            return matchSearch && matchCat && matchStatus;
        });
    }

    function getSorted(list) {
        return [...list].sort((a, b) => {
            let va = a[sortCol], vb = b[sortCol];
            if (sortCol === 'amount') { va = parseFloat(va); vb = parseFloat(vb); }
            else if (sortCol === 'date') { va = new Date(va); vb = new Date(vb); }
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

        tbody.innerHTML = paged.map(e => `
            <tr data-id="${e.id}">
                <td><span class="cat-badge">${esc(e.category)}</span></td>
                <td>${esc(e.description)}</td>
                <td>${formatDate(e.date)}</td>
                <td><span class="status-badge ${e.status.toLowerCase()}">${e.status}</span></td>
                <td class="expense-amount">-₱${parseFloat(e.amount).toLocaleString('en-PH', { minimumFractionDigits: 2 })}</td>
                <td>
                    <button class="btn-action-menu" data-id="${e.id}">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><circle cx="12" cy="5" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="12" cy="19" r="1"/></svg>
                    </button>
                </td>
            </tr>
        `).join('');

        tableCount.textContent = `Showing ${total} expense${total !== 1 ? 's' : ''}`;
        renderPagination(pages);
        updateSummary();
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

    function updateSummary() {
        const now   = new Date();
        const month = now.getMonth();
        const year  = now.getFullYear();

        const thisMonth = expenses.filter(e => {
            const d = new Date(e.date);
            return d.getMonth() === month && d.getFullYear() === year;
        });

        const total = thisMonth.reduce((sum, e) => sum + parseFloat(e.amount), 0);
        document.getElementById('statTotal').textContent = '₱' + total.toLocaleString('en-PH', { minimumFractionDigits: 2 });

        const stockTotal     = thisMonth.filter(e => e.category === 'Stock').reduce((s, e) => s + parseFloat(e.amount), 0);
        const utilitiesTotal = thisMonth.filter(e => e.category === 'Utilities').reduce((s, e) => s + parseFloat(e.amount), 0);
        const rentTotal      = thisMonth.filter(e => e.category === 'Rent').reduce((s, e) => s + parseFloat(e.amount), 0);

        document.getElementById('statStock').textContent     = '₱' + stockTotal.toLocaleString('en-PH', { minimumFractionDigits: 2 });
        document.getElementById('statUtilities').textContent = '₱' + utilitiesTotal.toLocaleString('en-PH', { minimumFractionDigits: 2 });
        document.getElementById('statRent').textContent      = '₱' + rentTotal.toLocaleString('en-PH', { minimumFractionDigits: 2 });

        const stockTotal     = thisMonth.filter(e => e.category === 'Stock').reduce((s, e) => s + parseFloat(e.amount), 0);
        const utilitiesTotal = thisMonth.filter(e => e.category === 'Utilities').reduce((s, e) => s + parseFloat(e.amount), 0);
        const rentTotal      = thisMonth.filter(e => e.category === 'Rent').reduce((s, e) => s + parseFloat(e.amount), 0);

        document.getElementById('statStock').textContent     = '₱' + stockTotal.toLocaleString('en-PH', { minimumFractionDigits: 2 });
        document.getElementById('statUtilities').textContent = '₱' + utilitiesTotal.toLocaleString('en-PH', { minimumFractionDigits: 2 });
        document.getElementById('statRent').textContent      = '₱' + rentTotal.toLocaleString('en-PH', { minimumFractionDigits: 2 });

        // Category breakdown
        const cats = {};
        thisMonth.forEach(e => { cats[e.category] = (cats[e.category] || 0) + parseFloat(e.amount); });

        const breakdownList = document.getElementById('breakdownList');
        breakdownList.innerHTML = Object.entries(cats).sort((a, b) => b[1] - a[1]).map(([cat, amt]) => `
            <div class="breakdown-item">
                <span class="breakdown-cat">${esc(cat)}</span>
                <span class="breakdown-amount">₱${amt.toLocaleString('en-PH', { minimumFractionDigits: 2 })}</span>
            </div>
        `).join('');

        document.getElementById('breakdownCard').classList.toggle('hidden', Object.keys(cats).length === 0);
    }

    /* ── Search ── */
    searchInput.addEventListener('input', () => {
        searchQuery = searchInput.value.trim();
        currentPage = 1;
        render();
    });

    /* ── Filter ── */
    btnFilter.addEventListener('click', e => { e.stopPropagation(); filterDropdown.classList.toggle('hidden'); });
    document.addEventListener('click', e => {
        if (!filterDropdown.contains(e.target) && e.target !== btnFilter) filterDropdown.classList.add('hidden');
    });

    btnApplyFilter.addEventListener('click', () => {
        const checks = filterDropdown.querySelectorAll('input[type=checkbox]');
        activeFilters.categories = [];
        activeFilters.statuses   = [];
        checks.forEach(cb => {
            if (!cb.checked) return;
            if (['Stock','Utilities','Rent','Staff','Maintenance','Other'].includes(cb.value)) activeFilters.categories.push(cb.value);
            if (['Paid','Pending'].includes(cb.value)) activeFilters.statuses.push(cb.value);
        });
        currentPage = 1;
        filterDropdown.classList.add('hidden');
        render();
    });

    /* ── Sort ── */
    document.querySelectorAll('th.sortable').forEach(th => {
        th.addEventListener('click', () => {
            const col = th.dataset.col;
            sortDir = sortCol === col ? (sortDir === 'asc' ? 'desc' : 'asc') : 'asc';
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
            actionMenu.style.left = (rect.left - 120 + window.scrollX) + 'px';
        } else if (!actionMenu.contains(e.target)) {
            actionMenu.classList.add('hidden');
        }
    });

    actionEdit.addEventListener('click', () => { actionMenu.classList.add('hidden'); openModal(actionTargetId); });

    actionDelete.addEventListener('click', async () => {
        actionMenu.classList.add('hidden');
        if (!confirm('Delete this expense?')) return;
        try {
            const res = await fetch('/api/expenses/delete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf() },
                body: JSON.stringify({ id: actionTargetId })
            });
            const data = await res.json();
            if (data.success) load();
        } catch (e) { alert('Failed to delete expense'); }
    });

    /* ── Modal ── */
    function openModal(id = null) {
        editingId = id;
        const exp = id ? expenses.find(e => e.id === id) : null;
        modalTitle.textContent = exp ? 'Edit Expense' : 'Record New Expense';

        document.getElementById('fieldDescription').value = exp ? exp.description : '';
        document.getElementById('fieldCategory').value    = exp ? exp.category    : 'Stock';
        document.getElementById('fieldStatus').value      = exp ? exp.status      : 'Paid';
        document.getElementById('fieldAmount').value      = exp ? exp.amount      : '';
        document.getElementById('fieldDate').value        = exp ? exp.date.substring(0, 10) : new Date().toISOString().substring(0, 10);

        modalBackdrop.classList.remove('hidden');
        document.getElementById('fieldDescription').focus();
    }

    function closeModal() { modalBackdrop.classList.add('hidden'); editingId = null; }

    btnAdd.addEventListener('click', () => openModal());
    modalClose.addEventListener('click', closeModal);
    btnCancel.addEventListener('click', closeModal);
    modalBackdrop.addEventListener('click', e => { if (e.target === modalBackdrop) closeModal(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

    btnSave.addEventListener('click', async () => {
        const description = document.getElementById('fieldDescription').value.trim();
        const category    = document.getElementById('fieldCategory').value;
        const status      = document.getElementById('fieldStatus').value;
        const amount      = parseFloat(document.getElementById('fieldAmount').value);
        const date        = document.getElementById('fieldDate').value;

        if (!description || isNaN(amount) || !date) { alert('Please fill in all fields.'); return; }

        const payload = { description, category, status, amount, date };
        if (editingId) payload.id = editingId;

        try {
            const res = await fetch('/api/expenses/save', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf() },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (data.success) { closeModal(); load(); }
            else alert('Error: ' + data.message);
        } catch (e) { alert('Failed to save expense'); }
    });

    /* ── Helpers ── */
    function formatDate(d) {
        return new Date(d).toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' });
    }

    function esc(str) {
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function csrf() {
        return document.querySelector('meta[name="csrf-token"]').content;
    }
});
