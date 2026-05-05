/**
 * Coffee POS – Dashboard Script
 */

document.addEventListener('DOMContentLoaded', async () => {

    /* ── Live Date ── */
    const dateEl = document.getElementById('headerDate');
    if (dateEl) {
        dateEl.textContent = new Date().toLocaleDateString('en-PH', {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
        });
    }

    let chartInstance = null;

    /* ── Load Dashboard Data ── */
    try {
        const res  = await fetch('/api/dashboard/stats');
        const data = await res.json();

        setText('totalSales',   '₱' + parseFloat(data.totalSales).toLocaleString('en-PH', { minimumFractionDigits: 2 }));
        setText('ordersToday',  data.ordersToday);
        setText('bestDrink',    data.bestProduct);
        setText('totalProducts', data.totalProducts);

        setChange('salesChange',  data.salesChange);
        setChange('ordersChange', data.ordersChange);

        const bestSub = document.getElementById('bestDrinkSub');
        if (bestSub) bestSub.textContent = data.bestProductSold + ' orders';

        const prodSub = document.getElementById('totalProductsSub');
        if (prodSub) prodSub.textContent = data.totalCategories + ' categories';

        chartInstance = renderChart(data.salesData);

        renderTopProducts(data.topProducts);
        renderTransactions(data.recentOrders);

    } catch (err) {
        console.error('Failed to load dashboard data:', err);
    }

    /* ── Chart Tab Switching ── */
    document.querySelectorAll('.chart-tab').forEach(btn => {
        btn.addEventListener('click', async () => {
            document.querySelectorAll('.chart-tab').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const period = btn.dataset.period;
            const days   = period === 'month' ? 30 : 7;

            try {
                const res  = await fetch(`/api/dashboard/stats?days=${days}`);
                const data = await res.json();
                if (chartInstance) { chartInstance.destroy(); chartInstance = null; }
                chartInstance = renderChart(data.salesData);
            } catch (err) {
                console.error('Failed to reload chart:', err);
            }
        });
    });

    /* ── Helpers ── */
    function setText(id, val) {
        const el = document.getElementById(id);
        if (el) el.textContent = val;
    }

    function setChange(id, pct) {
        const el = document.getElementById(id);
        if (!el) return;
        if (pct === null || pct === undefined) { el.innerHTML = '<span style="color:#a09080">No data yet</span>'; return; }
        const up = pct >= 0;
        const arrow = up
            ? '<svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="7 17 17 7"/><polyline points="7 7 17 7 17 17"/></svg>'
            : '<svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="7 7 17 17"/><polyline points="17 7 17 17 7 17"/></svg>';
        el.className = 'stat-change ' + (up ? 'positive' : 'negative');
        el.innerHTML = arrow + ` ${up ? '+' : ''}${pct}% from yesterday`;
    }

    /* ── Chart ── */
    function renderChart(salesData) {
        const ctx = document.getElementById('salesChart');
        if (!ctx) return null;

        const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 240);
        gradient.addColorStop(0, 'rgba(74, 44, 26, 0.18)');
        gradient.addColorStop(1, 'rgba(74, 44, 26, 0)');

        return new Chart(ctx, {
            type: 'line',
            data: {
                labels: salesData.map(d => new Date(d.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })),
                datasets: [{
                    label: 'Sales (₱)',
                    data: salesData.map(d => parseFloat(d.daily_total)),
                    borderColor: '#4a2c1a', backgroundColor: gradient,
                    borderWidth: 2.5, pointRadius: 4, pointHoverRadius: 7,
                    pointBackgroundColor: '#fff', pointBorderColor: '#4a2c1a',
                    pointBorderWidth: 2.5, tension: 0.42, fill: true,
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
                    x: { grid: { display: false }, ticks: { font: { family: "'Plus Jakarta Sans', sans-serif", size: 12 }, color: '#7a6555' }, border: { color: '#e8ddd4' } },
                    y: { grid: { color: '#f0ebe4' }, ticks: { font: { family: "'Plus Jakarta Sans', sans-serif", size: 12 }, color: '#7a6555', padding: 8, callback: v => '₱' + (v >= 1000 ? (v / 1000).toFixed(1) + 'k' : v) }, border: { display: false } }
                }
            }
        });
    }

    /* ── Top Products ── */
    function renderTopProducts(products) {
        const list = document.getElementById('productList');
        if (!list || !products.length) return;
        const max = products[0].sold || 1;
        list.innerHTML = products.map((p, i) => `
            <li class="product-item">
                <div class="product-rank">${i + 1}</div>
                <div class="product-info">
                    <span class="product-name">${p.name}</span>
                    <div class="product-bar-wrap"><div class="product-bar" style="width:0%" data-width="${Math.round((p.sold / max) * 100)}%"></div></div>
                </div>
                <span class="product-orders">${p.sold} orders</span>
            </li>
        `).join('');
        list.querySelectorAll('.product-bar').forEach((bar, i) => {
            setTimeout(() => { bar.style.width = bar.dataset.width; }, 300 + i * 80);
        });
    }

    /* ── Recent Transactions ── */
    function renderTransactions(orders) {
        const tbody = document.getElementById('txnTableBody');
        if (!tbody) return;
        if (!orders.length) {
            tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;color:#a09080;padding:24px">No transactions yet</td></tr>';
            return;
        }
        tbody.innerHTML = orders.map(o => `
            <tr>
                <td>#${o.order_number}</td>
                <td>${o.items_summary}</td>
                <td>₱${parseFloat(o.total).toLocaleString('en-PH', { minimumFractionDigits: 2 })}</td>
                <td><span class="badge paid">${o.status}</span></td>
            </tr>
        `).join('');
    }
});
