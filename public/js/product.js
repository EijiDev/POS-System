/**
 * Coffee POS – Products Page Script
 * File: public/js/products.js
 */

document.addEventListener('DOMContentLoaded', () => {

    /* ── Sample Data ── */
    let products = [];

    const LOW_STOCK_THRESHOLD = 10;
    const ROWS_PER_PAGE = 7;

    let searchQuery    = '';
    let activeFilters  = { categories: ['Coffee','Non-Coffee','Pastry','Snacks'], statuses: ['Active','Inactive'] };
    let sortCol        = '';
    let sortDir        = 'asc';
    let currentPage    = 1;
    let editingId      = null;
    let actionTargetId = null;

    /* ── Element Refs ── */
    const tbody         = document.getElementById('productTableBody');
    const emptyState    = document.getElementById('emptyState');
    const tableCount    = document.getElementById('tableCount');
    const pagination    = document.getElementById('pagination');
    const searchInput   = document.getElementById('searchInput');
    const btnFilter     = document.getElementById('btnFilter');
    const filterDropdown= document.getElementById('filterDropdown');
    const btnApplyFilter= document.getElementById('btnApplyFilter');
    const btnAdd        = document.getElementById('btnAddProduct');
    const modalBackdrop = document.getElementById('modalBackdrop');
    const modalTitle    = document.getElementById('modalTitle');
    const modalClose    = document.getElementById('modalClose');
    const btnCancel     = document.getElementById('btnCancel');
    const btnSave       = document.getElementById('btnSave');
    const actionMenu    = document.getElementById('actionMenu');
    const actionEdit    = document.getElementById('actionEdit');
    const actionToggle  = document.getElementById('actionToggle');
    const actionDelete  = document.getElementById('actionDelete');

    /* ── Init ── */
    async function loadProducts() {
        try {
            const response = await fetch('/api/products');
            const data = await response.json();
            products = data.map(p => ({
                id: p.product_id,
                name: p.product_name,
                category: p.category,
                price: parseFloat(p.price),
                cost: parseFloat(p.cost),
                stock: parseInt(p.stock),
                sold: parseInt(p.sold),
                status: p.status,
                image_url: p.image_url
            }));
            render();
        } catch (error) {
            console.error('Failed to load products:', error);
        }
    }

    loadProducts();

    /* ── Render ── */
    function getFiltered() {
        return products.filter(p => {
            const matchSearch = !searchQuery ||
                p.name.toLowerCase().includes(searchQuery) ||
                p.category.toLowerCase().includes(searchQuery);
            const matchCat    = activeFilters.categories.includes(p.category);
            const matchStatus = activeFilters.statuses.includes(p.status);
            return matchSearch && matchCat && matchStatus;
        });
    }

    function getSorted(list) {
        if (!sortCol) return list;
        return [...list].sort((a, b) => {
            let va = a[sortCol], vb = b[sortCol];
            if (typeof va === 'string') va = va.toLowerCase(), vb = vb.toLowerCase();
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

        // Empty state
        emptyState.classList.toggle('hidden', paged.length > 0);

        // Rows
        tbody.innerHTML = paged.map(p => `
            <tr data-id="${p.id}">
                <td>
                    <div class="product-thumb"></div>
                </td>
                <td>
                    <div class="product-name-wrap">
                        <span class="pname">${esc(p.name)}</span>
                        <span class="psub">${p.sold.toLocaleString()} units sold</span>
                    </div>
                </td>
                <td><span class="cat-badge">${esc(p.category)}</span></td>
                <td>₱${p.price.toFixed(2)}</td>
                <td>₱${p.cost.toFixed(2)}</td>
                <td class="${p.stock <= LOW_STOCK_THRESHOLD ? 'stock-warn' : ''}">${p.stock}</td>
                <td>
                    <span class="status-dot ${p.status === 'Active' ? 'active' : 'inactive'}">
                        ${p.status}
                    </span>
                </td>
                <td>
                    <button class="btn-action-menu" data-id="${p.id}" aria-label="Actions">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><circle cx="12" cy="5" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="12" cy="19" r="1"/></svg>
                    </button>
                </td>
            </tr>
        `).join('');

        // Count
        tableCount.textContent = `Showing ${start + 1}–${Math.min(start + paged.length, total)} of ${total} product${total !== 1 ? 's' : ''}`;

        // Pagination
        renderPagination(pages);

        // Update stat cards
        updateStats();
    }

    function renderPagination(pages) {
        if (pages <= 1) { pagination.innerHTML = ''; return; }
        let html = '';
        for (let i = 1; i <= pages; i++) {
            html += `<button class="page-btn ${i === currentPage ? 'active' : ''}" data-page="${i}">${i}</button>`;
        }
        pagination.innerHTML = html;
        pagination.querySelectorAll('.page-btn').forEach(btn => {
            btn.addEventListener('click', () => { currentPage = +btn.dataset.page; render(); });
        });
    }

    function updateStats() {
        const active  = products.filter(p => p.status === 'Active');
        const lowStock= products.filter(p => p.stock <= LOW_STOCK_THRESHOLD).length;
        const margins = active.map(p => ((p.price - p.cost) / p.price) * 100);
        const avgMargin = margins.length ? margins.reduce((a,b) => a+b,0) / margins.length : 0;
        const totalVal  = products.reduce((sum, p) => sum + p.price * p.stock, 0);

        const statCards = document.querySelectorAll('.stat-value');
        if (statCards[0]) statCards[0].textContent = active.length;
        if (statCards[1]) statCards[1].textContent = Math.round(avgMargin) + '%';
        if (statCards[2]) statCards[2].textContent = lowStock;
        if (statCards[3]) statCards[3].textContent = '₱' + (totalVal >= 1000 ? (totalVal/1000).toFixed(1)+'K' : totalVal.toFixed(0));
    }

    function esc(str) {
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    /* ── Search ── */
    searchInput.addEventListener('input', () => {
        searchQuery = searchInput.value.trim().toLowerCase();
        currentPage = 1;
        render();
    });

    /* ── Filter ── */
    btnFilter.addEventListener('click', (e) => {
        e.stopPropagation();
        filterDropdown.classList.toggle('hidden');
    });

    document.addEventListener('click', (e) => {
        if (!filterDropdown.contains(e.target) && e.target !== btnFilter) {
            filterDropdown.classList.add('hidden');
        }
    });

    btnApplyFilter.addEventListener('click', () => {
        const catChecks = filterDropdown.querySelectorAll('input[type=checkbox]');
        activeFilters.categories = [];
        activeFilters.statuses   = [];
        catChecks.forEach(cb => {
            if (!cb.checked) return;
            if (['Coffee','Non-Coffee','Pastry','Snacks'].includes(cb.value)) activeFilters.categories.push(cb.value);
            if (['Active','Inactive'].includes(cb.value)) activeFilters.statuses.push(cb.value);
        });
        currentPage = 1;
        filterDropdown.classList.add('hidden');
        render();
    });

    /* ── Sort ── */
    document.querySelectorAll('th.sortable').forEach(th => {
        th.addEventListener('click', () => {
            const col = th.dataset.col;
            if (sortCol === col) {
                sortDir = sortDir === 'asc' ? 'desc' : 'asc';
            } else {
                sortCol = col;
                sortDir = 'asc';
            }
            document.querySelectorAll('.sort-arrow').forEach(a => { a.className = 'sort-arrow'; });
            const arrow = th.querySelector('.sort-arrow');
            if (arrow) arrow.className = `sort-arrow ${sortDir}`;
            render();
        });
    });

    /* ── Action Menu ── */
    document.addEventListener('click', (e) => {
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

    actionEdit.addEventListener('click', () => {
        actionMenu.classList.add('hidden');
        openModal(actionTargetId);
    });

    actionToggle.addEventListener('click', async () => {
        actionMenu.classList.add('hidden');
        const p = products.find(p => p.id === actionTargetId);
        if (p) {
            const newStatus = p.status === 'Active' ? 'Inactive' : 'Active';
            try {
                const response = await fetch('/api/products/save', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        product_id: p.id,
                        product_name: p.name,
                        category: p.category,
                        price: p.price,
                        cost: p.cost,
                        stock: p.stock,
                        status: newStatus,
                        image_url: p.image_url || ''
                    })
                });
                const result = await response.json();
                if (result.success) {
                    loadProducts();
                }
            } catch (error) {
                alert('Failed to update status');
            }
        }
    });

    actionDelete.addEventListener('click', async () => {
        actionMenu.classList.add('hidden');
        if (confirm('Delete this product?')) {
            try {
                const response = await fetch('/api/products/delete', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ product_id: actionTargetId })
                });
                const result = await response.json();
                if (result.success) {
                    loadProducts();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('Failed to delete product');
            }
        }
    });

    /* ── Modal ── */
    function openModal(id = null) {
        editingId = id;
        const p = id ? products.find(p => p.id === id) : null;
        modalTitle.textContent = p ? 'Edit Product' : 'Add New Product';

        document.getElementById('fieldName').value     = p ? p.name     : '';
        document.getElementById('fieldCategory').value = p ? p.category : 'Coffee';
        document.getElementById('fieldPrice').value    = p ? p.price    : '';
        document.getElementById('fieldCost').value     = p ? p.cost     : '';
        document.getElementById('fieldStock').value    = p ? p.stock    : '';
        document.getElementById('fieldStatus').value   = p ? p.status   : 'Active';
        document.getElementById('fieldImage').value    = '';
        document.getElementById('fieldImageUrl').value = p ? (p.image_url || '') : '';

        modalBackdrop.classList.remove('hidden');
        document.getElementById('fieldName').focus();
    }

    function closeModal() {
        modalBackdrop.classList.add('hidden');
        editingId = null;
    }

    btnAdd.addEventListener('click', () => openModal());
    modalClose.addEventListener('click', closeModal);
    btnCancel.addEventListener('click', closeModal);
    modalBackdrop.addEventListener('click', (e) => { if (e.target === modalBackdrop) closeModal(); });

    btnSave.addEventListener('click', async () => {
        const name     = document.getElementById('fieldName').value.trim();
        const category = document.getElementById('fieldCategory').value;
        const price    = parseFloat(document.getElementById('fieldPrice').value);
        const cost     = parseFloat(document.getElementById('fieldCost').value);
        const stock    = parseInt(document.getElementById('fieldStock').value);
        const status   = document.getElementById('fieldStatus').value;
        const imageFile = document.getElementById('fieldImage').files[0];
        const existingImageUrl = document.getElementById('fieldImageUrl').value;

        if (!name || isNaN(price) || isNaN(cost) || isNaN(stock)) {
            alert('Please fill in all fields correctly.'); return;
        }

        let imageUrl = existingImageUrl;

        // If new image is uploaded, convert to base64
        if (imageFile) {
            imageUrl = await new Promise((resolve) => {
                const reader = new FileReader();
                reader.onload = (e) => resolve(e.target.result);
                reader.readAsDataURL(imageFile);
            });
        }

        const productData = {
            product_name: name,
            category: category,
            price: price,
            cost: cost,
            stock: stock,
            status: status,
            image_url: imageUrl
        };

        if (editingId) {
            productData.product_id = editingId;
        }

        try {
            const response = await fetch('/api/products/save', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(productData)
            });
            const result = await response.json();
            if (result.success) {
                closeModal();
                loadProducts();
            } else {
                alert('Error: ' + result.message);
            }
        } catch (error) {
            alert('Failed to save product');
        }
    });

    /* ── Init ── */
    render();
});
