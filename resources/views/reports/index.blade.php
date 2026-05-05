@extends('layouts.app')

@section('title', 'Reports – BrewPOS')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/reports.css') }}">
@endpush

@section('content')
<main class="main-content">

    <div class="page-header">
        <div>
            <h1 class="page-title">Analytics & Reports</h1>
            <p class="page-subtitle">Insights into your coffee shop performance</p>
        </div>
        <div class="header-actions">
            <div class="period-tabs">
                <button class="period-tab active" data-days="30">Last 30 Days</button>
                <button class="period-tab" data-days="7">Last 7 Days</button>
                <button class="period-tab" data-days="90">Last 90 Days</button>
            </div>
            <button class="btn-export" id="btnExport">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Export Excel
            </button>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="stat-grid">
        <div class="stat-card" style="--delay:0s">
            <div class="stat-top">
                <span class="stat-label">Total Revenue</span>
                <div class="stat-icon-wrap" style="background:#edf7f1">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2e7d50" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
            </div>
            <div class="stat-value" id="statRevenue">₱0.00</div>
            <div class="stat-change" id="statRevenueChange"></div>
        </div>
        <div class="stat-card" style="--delay:0.07s">
            <div class="stat-top">
                <span class="stat-label">Avg. Order Value</span>
                <div class="stat-icon-wrap" style="background:#eaf1fd">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="2"/></svg>
                </div>
            </div>
            <div class="stat-value" id="statAvgOrder">₱0.00</div>
            <div class="stat-change" id="statAvgChange"></div>
        </div>
        <div class="stat-card" style="--delay:0.14s">
            <div class="stat-top">
                <span class="stat-label">Gross Profit</span>
                <div class="stat-icon-wrap" style="background:#f0ebe4">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b4423" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                </div>
            </div>
            <div class="stat-value" id="statProfit">₱0.00</div>
            <div class="stat-sub" id="statMargin">0% Margin</div>
        </div>
        <div class="stat-card" style="--delay:0.21s">
            <div class="stat-top">
                <span class="stat-label">Total Expenses</span>
                <div class="stat-icon-wrap" style="background:#fef2f0">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#c0392b" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                </div>
            </div>
            <div class="stat-value" id="statExpenses">₱0.00</div>
            <div class="stat-sub" id="statExpenseSub">—</div>
        </div>
    </div>

    {{-- Chart Tabs --}}
    <div class="report-card" id="chartCard">
        <div class="report-card-header">
            <div class="report-tabs">
                <button class="report-tab active" data-tab="revenue">Revenue Over Time</button>
                <button class="report-tab" data-tab="popular">Popular Items</button>
                <button class="report-tab" data-tab="category">Sales by Category</button>
                <button class="report-tab" data-tab="orders">Orders</button>
            </div>
        </div>

        {{-- Revenue Chart --}}
        <div class="tab-panel active" id="tabRevenue">
            <div class="chart-body">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        {{-- Popular Items --}}
        <div class="tab-panel hidden" id="tabPopular">
            <div class="popular-list" id="popularList"></div>
        </div>

        {{-- Category Breakdown --}}
        <div class="tab-panel hidden" id="tabCategory">
            <div class="category-grid" id="categoryGrid"></div>
        </div>

        {{-- Orders Table --}}
        <div class="tab-panel hidden" id="tabOrders">
            <div class="table-wrap">
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Date & Time</th>
                            <th>Items</th>
                            <th>Total (₱)</th>
                            <th>Payment</th>
                        </tr>
                    </thead>
                    <tbody id="ordersTableBody"></tbody>
                </table>
            </div>
            <div class="table-footer">
                <span class="table-count" id="ordersCount">—</span>
                <div class="pagination" id="ordersPagination"></div>
            </div>
        </div>
    </div>

</main>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <script src="{{ asset('js/reports.js') }}"></script>
@endpush
