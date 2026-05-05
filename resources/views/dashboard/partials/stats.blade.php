<!-- Stat Cards -->
<div class="stat-grid">
    <div class="stat-card" style="--delay:0s">
        <div class="stat-top">
            <span class="stat-label">Total Sales Today</span>
            <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/>
            </svg>
        </div>
        <div class="stat-value" id="totalSales">₱0.00</div>
        <div class="stat-change" id="salesChange"></div>
    </div>

    <div class="stat-card" style="--delay:0.07s">
        <div class="stat-top">
            <span class="stat-label">Orders Today</span>
            <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>
                <rect x="9" y="3" width="6" height="4" rx="2"/><line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/>
            </svg>
        </div>
        <div class="stat-value" id="ordersToday">0</div>
        <div class="stat-change" id="ordersChange"></div>
    </div>

    <div class="stat-card" style="--delay:0.14s">
        <div class="stat-top">
            <span class="stat-label">Best-selling Drink</span>
            <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
            </svg>
        </div>
        <div class="stat-value stat-text" id="bestDrink">—</div>
        <div class="stat-sub" id="bestDrinkSub">0 orders</div>
    </div>

    <div class="stat-card" style="--delay:0.21s">
        <div class="stat-top">
            <span class="stat-label">Total Products</span>
            <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
            </svg>
        </div>
        <div class="stat-value" id="totalProducts">0</div>
        <div class="stat-sub" id="totalProductsSub">0 categories</div>
    </div>
</div>
