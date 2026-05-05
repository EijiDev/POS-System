'use strict';

document.addEventListener('DOMContentLoaded', () => {

    let currentDays   = 30;
    let chartInstance = null;
    let allOrders     = [];
    let ordersPage    = 1;
    const ORDERS_PER_PAGE = 15;

    /* ── Load ── */
    async function load() {
        try {
            const res = await fetch(`/api/reports/stats?days=${currentDays}`);

            if (!res.ok) {
                console.error('Stats API returned', res.status, res.statusText);
                return;
            }

            const data = await res.json();
            renderStats(data);
            renderRevenueChart(data.chartData);
            renderPopular(data.popularItems);
            renderCategories(data.byCategory);
            allOrders  = data.orders || [];
            ordersPage = 1;
            renderOrdersTable();
        } catch (e) {
            console.error('Failed to load report data:', e);
        }
    }

    load();

    /* ── Period Tabs ── */
    document.querySelectorAll('.period-tab').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.period-tab').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentDays = +btn.dataset.days;
            load();
        });
    });

    /* ── Report Tabs ── */
    document.querySelectorAll('.report-tab').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.report-tab').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
            btn.classList.add('active');
            document.getElementById('tab' + capitalize(btn.dataset.tab)).classList.remove('hidden');
        });
    });

    /* ── Export Excel ── */
    document.getElementById('btnExport').addEventListener('click', () => {
        window.location.href = `/api/reports/export-excel?days=${currentDays}`;
    });

    /* ── Render Stats ── */
    function renderStats(data) {
        setText('statRevenue',  fmt(data.revenue));
        setText('statAvgOrder', fmt(data.avgOrder));
        setText('statProfit',   fmt(data.grossProfit));
        setText('statExpenses', fmt(data.expenses));
        setText('statMargin',   data.margin + '% Margin');

        const expCats = data.expenseBreakdown.map(e => e.category).slice(0, 3).join(', ');
        setText('statExpenseSub', expCats || '—');

        setChange('statRevenueChange', data.revenueChange, 'from last period');
        setChange('statAvgChange',     data.avgChange,     'from last period');
    }

    /* ── Revenue Chart ── */
    function renderRevenueChart(chartData) {
        const ctx = document.getElementById('revenueChart');
        if (!ctx) return;
        if (chartInstance) { chartInstance.destroy(); chartInstance = null; }

        const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(107, 68, 35, 0.15)');
        gradient.addColorStop(1, 'rgba(107, 68, 35, 0)');

        chartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.map(d => new Date(d.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })),
                datasets: [{
                    label: 'Revenue (₱)',
                    data: chartData.map(d => d.total),
                    borderColor: '#6b4423', backgroundColor: gradient,
                    borderWidth: 2.5, pointRadius: currentDays <= 7 ? 5 : 3,
                    pointHoverRadius: 7, pointBackgroundColor: '#fff',
                    pointBorderColor: '#6b4423', pointBorderWidth: 2,
                    tension: 0.42, fill: true,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#fff', titleColor: '#2c1a0e', bodyColor: '#7a6555',
                        borderColor: '#e8ddd4', borderWidth: 1, padding: 12, cornerRadius: 10,
                        titleFont: { family: "'Plus Jakarta Sans', sans-serif", weight: '600', size: 13 },
                        bodyFont:  { family: "'Plus Jakarta Sans', sans-serif", size: 12 },
                        callbacks: { label: c => ` ₱${c.parsed.y.toLocaleString('en-PH', { minimumFractionDigits: 2 })}` }
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { family: "'Plus Jakarta Sans', sans-serif", size: 11 }, color: '#7a6555', maxTicksLimit: 10 }, border: { color: '#e8ddd4' } },
                    y: { grid: { color: '#f0ebe4' }, ticks: { font: { family: "'Plus Jakarta Sans', sans-serif", size: 11 }, color: '#7a6555', padding: 8, callback: v => '₱' + (v >= 1000 ? (v/1000).toFixed(1)+'k' : v) }, border: { display: false } }
                }
            }
        });
    }

    /* ── Popular Items ── */
    function renderPopular(items) {
        const list = document.getElementById('popularList');
        if (!list) return;
        if (!items.length) { list.innerHTML = '<p style="color:#a09080;text-align:center;padding:32px">No sales data yet</p>'; return; }

        const maxQty = items[0].total_qty || 1;
        list.innerHTML = items.map((item, i) => `
            <div class="popular-item">
                <div class="popular-rank ${i < 3 ? 'top' : ''}">${i + 1}</div>
                <div class="popular-info">
                    <span class="popular-name">${esc(item.product_name)}</span>
                    <div class="popular-bar-bg"><div class="popular-bar" style="width:0%" data-width="${Math.round((item.total_qty / maxQty) * 100)}%"></div></div>
                </div>
                <div class="popular-meta">
                    <span class="popular-qty">${item.total_qty} sold</span>
                    <span class="popular-rev">₱${parseFloat(item.total_revenue).toLocaleString('en-PH', { minimumFractionDigits: 2 })}</span>
                </div>
            </div>
        `).join('');

        list.querySelectorAll('.popular-bar').forEach((bar, i) => {
            setTimeout(() => { bar.style.width = bar.dataset.width; }, 200 + i * 60);
        });
    }

    /* ── Categories ── */
    function renderCategories(cats) {
        const grid = document.getElementById('categoryGrid');
        if (!grid) return;
        if (!cats.length) { grid.innerHTML = '<p style="color:#a09080;text-align:center;padding:32px;grid-column:1/-1">No data yet</p>'; return; }

        const maxRev = cats[0].total_revenue || 1;
        grid.innerHTML = cats.map(c => `
            <div class="category-item">
                <span class="category-name">${esc(c.category)}</span>
                <span class="category-revenue">₱${parseFloat(c.total_revenue).toLocaleString('en-PH', { minimumFractionDigits: 2 })}</span>
                <span class="category-qty">${c.total_qty} items sold</span>
                <div class="category-bar-bg"><div class="category-bar" style="width:0%" data-width="${Math.round((c.total_revenue / maxRev) * 100)}%"></div></div>
            </div>
        `).join('');

        grid.querySelectorAll('.category-bar').forEach((bar, i) => {
            setTimeout(() => { bar.style.width = bar.dataset.width; }, 200 + i * 60);
        });
    }

    /* ── Orders Table ── */
    function renderOrdersTable() {
        const tbody      = document.getElementById('ordersTableBody');
        const countEl    = document.getElementById('ordersCount');
        const pagEl      = document.getElementById('ordersPagination');
        if (!tbody) return;

        const total  = allOrders.length;
        const pages  = Math.max(1, Math.ceil(total / ORDERS_PER_PAGE));
        ordersPage   = Math.min(ordersPage, pages);
        const start  = (ordersPage - 1) * ORDERS_PER_PAGE;
        const paged  = allOrders.slice(start, start + ORDERS_PER_PAGE);

        if (!paged.length) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:#a09080;padding:32px">No orders in this period</td></tr>';
            countEl.textContent = '0 orders';
            pagEl.innerHTML = '';
            return;
        }

        tbody.innerHTML = paged.map(o => `
            <tr>
                <td><strong>#${o.order_number}</strong></td>
                <td>${o.date}</td>
                <td class="items-cell" title="${esc(o.items_summary)}">${esc(o.items_summary)}</td>
                <td><strong>₱${parseFloat(o.total).toLocaleString('en-PH', { minimumFractionDigits: 2 })}</strong></td>
                <td>${o.payment}</td>
            </tr>
        `).join('');

        countEl.textContent = `Showing ${start + 1}–${Math.min(start + paged.length, total)} of ${total} orders`;

        // Pagination
        if (pages <= 1) { pagEl.innerHTML = ''; return; }
        const maxVisible = 7;
        let pagHTML = '';
        for (let i = 1; i <= pages; i++) {
            if (pages > maxVisible && i > 3 && i < pages - 2 && Math.abs(i - ordersPage) > 1) {
                if (i === 4 || i === pages - 3) pagHTML += `<span style="padding:0 4px;color:#a09080">…</span>`;
                continue;
            }
            pagHTML += `<button class="page-btn ${i === ordersPage ? 'active' : ''}" data-page="${i}">${i}</button>`;
        }
        pagEl.innerHTML = pagHTML;
        pagEl.querySelectorAll('.page-btn').forEach(btn =>
            btn.addEventListener('click', () => { ordersPage = +btn.dataset.page; renderOrdersTable(); })
        );
    }

    /* ── Helpers ── */
    function fmt(val) { return '₱' + parseFloat(val || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 }); }
    function setText(id, val) { const el = document.getElementById(id); if (el) el.textContent = val; }

    function setChange(id, pct, label) {
        const el = document.getElementById(id);
        if (!el) return;
        if (pct === null || pct === undefined) { el.innerHTML = '<span style="color:#a09080">No prior data</span>'; return; }
        const up = pct >= 0;
        const arrow = up
            ? '<svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="7 17 17 7"/><polyline points="7 7 17 7 17 17"/></svg>'
            : '<svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="7 7 17 17"/><polyline points="17 7 17 17 7 17"/></svg>';
        el.className = 'stat-change ' + (up ? 'positive' : 'negative');
        el.innerHTML = `${arrow} ${up ? '+' : ''}${pct}% ${label}`;
    }

    function capitalize(s) { return s.charAt(0).toUpperCase() + s.slice(1); }
    function esc(str) { return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
});
